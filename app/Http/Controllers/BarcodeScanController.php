<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Product;
use App\Utils\BusinessUtil;
use App\Utils\CashRegisterUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Premium barcode-scan POS.
 *
 * Three responsibilities, all on TOP of the existing UltimatePOS engine:
 *   index()    – render the scan screen.
 *   lookup()   – identify ONE product from a scanned code (read-only, no stock change).
 *   checkout() – finalise the cart through the exact same util methods SellPosController@store
 *                uses, so stock decrements / history / payments stay single-source-of-truth.
 *
 * Why a separate controller: keeps the new screen isolated, but it reuses ProductUtil /
 * TransactionUtil so there is NO second stock implementation to keep in sync.
 */
class BarcodeScanController extends Controller
{
    protected $productUtil;
    protected $transactionUtil;
    protected $contactUtil;
    protected $businessUtil;
    protected $cashRegisterUtil;
    protected $moduleUtil;

    public function __construct(
        ProductUtil $productUtil,
        TransactionUtil $transactionUtil,
        ContactUtil $contactUtil,
        BusinessUtil $businessUtil,
        CashRegisterUtil $cashRegisterUtil,
        ModuleUtil $moduleUtil
    ) {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Scan screen. Picks the cashier's current register location (falls back to first
     * permitted location) exactly like SellPosController@create.
     */
    public function index()
    {
        if (! auth()->user()->can('sell.create') && ! auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $register = $this->cashRegisterUtil->getCurrentCashRegister(auth()->user()->id);
        $default_location = ! empty($register->location_id)
            ? BusinessLocation::find($register->location_id)
            : null;

        if (empty($default_location)) {
            $locations = BusinessLocation::forDropdown($business_id, false, true)['locations'];
            foreach (array_keys($locations) as $id) {
                $default_location = BusinessLocation::find($id);
                break;
            }
        }

        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
        $register_open = $this->cashRegisterUtil->countOpenedRegister() > 0;

        return view('sale_pos.scan', compact('default_location', 'walk_in_customer', 'register_open'));
    }

    /**
     * Identify a product from a scanned barcode.
     *
     * The barcode IDENTIFIES the product only — every price/stock value below is read
     * straight from the DB, never from the scanner. No stock is touched here: scanning is
     * a pure read. Stock changes happen only in checkout() when the sale is confirmed.
     *
     * Barcode == variation `sub_sku` (UltimatePOS' scannable code). Matched exactly so a
     * USB scanner's full code resolves to a single item.
     */
    public function lookup(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $barcode = trim((string) $request->get('barcode', ''));
        $location_id = $request->get('location_id');

        if ($barcode === '') {
            return response()->json(['found' => false, 'msg' => 'Empty barcode'], 422);
        }

        // Join product -> matching variation (by sub_sku) -> stock row for this location.
        $row = Product::active()
            ->where('products.business_id', $business_id)
            ->where('products.type', '!=', 'modifier')
            ->join('variations as v', 'v.product_id', '=', 'products.id')
            ->leftJoin('variation_location_details as vld', function ($join) use ($location_id) {
                $join->on('vld.variation_id', '=', 'v.id');
                if (! empty($location_id)) {
                    $join->where('vld.location_id', $location_id);
                }
            })
            ->whereNull('v.deleted_at')
            ->where('v.sub_sku', $barcode)
            ->with(['brand', 'category'])
            ->select(
                'products.*',
                'v.id as variation_id',
                'v.name as variation_name',
                'v.sub_sku',
                'v.sell_price_inc_tax as selling_price',
                'v.default_purchase_price as purchase_price',
                'vld.qty_available'
            )
            ->first();

        if (empty($row)) {
            // Not found -> the UI shows the "Create product with this barcode" empty state.
            return response()->json([
                'found' => false,
                'barcode' => $barcode,
                'create_url' => action([\App\Http\Controllers\ProductController::class, 'create']),
            ], 404);
        }

        $qty = (float) ($row->qty_available ?? 0);
        $min = (float) ($row->alert_quantity ?? 0);
        $status = self::stockStatus((bool) $row->enable_stock, $qty, $min);

        // Optical-specific details, only the ones that are filled in.
        $optical = array_filter([
            'type' => $row->optical_product_type,
            'frame_color' => $row->frame_color,
            'eye_size' => $row->frame_eye_size,
            'bridge_size' => $row->frame_bridge_size,
            'temple_length' => $row->frame_temple_length,
            'lens_material' => $row->lens_material,
            'lens_coating' => $row->lens_coating,
            'lens_type' => $row->lens_type,
            'lens_index' => $row->lens_index,
        ], fn ($v) => ! empty($v));

        return response()->json([
            'found' => true,
            'product' => [
                'product_id' => $row->id,
                'variation_id' => $row->variation_id,
                'name' => $row->name,
                'variation' => $row->variation_name !== 'DUMMY' ? $row->variation_name : null,
                'brand' => optional($row->brand)->name,
                'sku' => $row->sku,
                'barcode' => $row->sub_sku,
                'category' => optional($row->category)->name,
                'selling_price' => (float) $row->selling_price,
                'selling_price_formatted' => $this->transactionUtil->num_f($row->selling_price, true),
                'stock_quantity' => $qty,
                'min_stock' => $min,
                'enable_stock' => (bool) $row->enable_stock,
                'image_url' => $row->image_url,
                'optical' => $optical,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Pure stock-status rule (badge colour). Service items (no stock tracking) are always
     * sellable; otherwise: <=0 out, <=min low, else in stock.
     */
    public static function stockStatus(bool $enableStock, float $qty, float $min): string
    {
        if (! $enableStock) {
            return 'in_stock';
        }
        if ($qty <= 0) {
            return 'out_of_stock';
        }
        if ($min > 0 && $qty <= $min) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    /**
     * Confirm the sale.
     *
     * STOCK DECREASES ONLY HERE — never on scan. Reason: scanning just builds a cart
     * (a quote); the goods are not gone until the cashier takes payment. Decrementing on
     * scan would corrupt stock on every abandoned/edited cart. So the single write to
     * stock is gated behind this confirmation.
     *
     * Reuses the same util calls as SellPosController@store (createSellTransaction,
     * createOrUpdateSellLines, decreaseProductQuantity, mapPurchaseSell) inside one DB
     * transaction => identical stock math, payment records and stock-mapping history.
     */
    public function checkout(Request $request)
    {
        if (! auth()->user()->can('sell.create') && ! auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');
        $location_id = $request->input('location_id');
        $cart = $request->input('cart', []);

        if (empty($cart)) {
            return response()->json(['success' => 0, 'msg' => 'Cart is empty'], 422);
        }

        // Cash sale needs an open register, same gate as SellPosController. Else the cash
        // taken never lands in a register -> Z-report / reconciliation hole.
        if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
            return response()->json([
                'success' => 0,
                'msg' => 'No open cash register. Open a register before selling.',
            ], 422);
        }

        $allow_overselling = ! empty($request->session()->get('business.pos_settings')['allow_overselling']);

        try {
            DB::beginTransaction();

            // Build the products[] payload in the exact shape createOrUpdateSellLines expects,
            // re-reading price/stock from the DB so nothing is trusted from the client.
            $products = [];
            foreach ($cart as $line) {
                $qty = (float) $line['quantity'];
                if ($qty <= 0) {
                    continue;
                }

                $variation = \App\Variation::with('product')->find($line['variation_id']);
                if (empty($variation) || $variation->product->business_id != $business_id) {
                    DB::rollBack();
                    return response()->json(['success' => 0, 'msg' => 'Invalid product in cart'], 422);
                }

                $product = $variation->product;

                // Server-side oversell guard (price/stock are authoritative from DB).
                if ($product->enable_stock && ! $allow_overselling) {
                    $available = (float) \App\VariationLocationDetails::where('variation_id', $variation->id)
                        ->where('location_id', $location_id)
                        ->lockForUpdate()
                        ->value('qty_available');
                    if ($qty > $available) {
                        DB::rollBack();
                        return response()->json([
                            'success' => 0,
                            'msg' => "Not enough stock for {$product->name}. Available: {$available}",
                        ], 422);
                    }
                }

                // Per-unit tax = inc-tax price minus exclusive price. tax_id = product's
                // tax group. Same values main POS records, so output-tax reports stay right.
                $item_tax = (float) $variation->sell_price_inc_tax - (float) $variation->default_sell_price;

                $products[] = [
                    'product_id' => $product->id,
                    'variation_id' => $variation->id,
                    'product_type' => $product->type,
                    'enable_stock' => $product->enable_stock,
                    'quantity' => $qty,
                    'unit_price' => $variation->sell_price_inc_tax,
                    'unit_price_inc_tax' => $variation->sell_price_inc_tax,
                    'item_tax' => $item_tax,
                    'tax_id' => $product->tax,
                    'base_unit_multiplier' => 1,
                    'sell_line_note' => '',
                ];
            }

            if (empty($products)) {
                DB::rollBack();
                return response()->json(['success' => 0, 'msg' => 'Cart is empty'], 422);
            }

            $contact_id = $request->input('contact_id')
                ?: optional($this->contactUtil->getWalkInCustomer($business_id, false))->id;

            $discount = ['discount_type' => 'fixed', 'discount_amount' => 0];
            // uf_number=false: our prices/quantities are raw numbers, not formatted strings.
            $invoice_total = $this->productUtil->calculateInvoiceTotal($products, null, $discount, false);

            $input = [
                'business_id' => $business_id,
                'location_id' => $location_id,
                'contact_id' => $contact_id,
                'transaction_date' => \Carbon::now(),
                'status' => 'final',
                'is_quotation' => 0,
                'sub_status' => null,
                'discount_type' => 'fixed',
                'discount_amount' => 0,
                'tax_rate_id' => null,
                'tax_amount' => 0,
                'final_total' => $invoice_total['final_total'],
                'sale_note' => null,
                'staff_note' => null,
                'shipping_charges' => 0,
                'shipping_details' => null,
                'products' => $products,
            ];

            // uf_data=false throughout: values are already raw numbers/dates, skip the
            // form-string parsing the existing POS needs.
            $transaction = $this->transactionUtil->createSellTransaction($business_id, $input, $invoice_total, $user_id, false);

            $this->transactionUtil->createOrUpdateSellLines($transaction, $products, $location_id, false, null, [], false);

            // Single cash payment for the full total.
            $payment = [[
                'amount' => $transaction->final_total,
                'method' => 'cash',
                'paid_on' => \Carbon::now()->toDateTimeString(),
                'is_return' => 0,
            ]];
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payment, null, null, false);

            // The one stock write — see method docblock for why it lives only here.
            foreach ($products as $product) {
                if ($product['enable_stock']) {
                    $this->productUtil->decreaseProductQuantity(
                        $product['product_id'],
                        $product['variation_id'],
                        $location_id,
                        $product['quantity']
                    );
                }
            }

            // Record payment against the open register (if any).
            if ($this->cashRegisterUtil->countOpenedRegister() > 0) {
                $this->cashRegisterUtil->addSellPayments($transaction, $payment);
            }

            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            // Allocate purchase->sell mapping = stock movement history.
            $business_details = $this->businessUtil->getDetails($business_id);
            $pos_settings = empty($business_details->pos_settings)
                ? $this->businessUtil->defaultPosSettings()
                : json_decode($business_details->pos_settings, true);
            $business = [
                'id' => $business_id,
                'accounting_method' => $request->session()->get('business.accounting_method'),
                'location_id' => $location_id,
                'pos_settings' => $pos_settings,
            ];
            $this->transactionUtil->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');

            $this->transactionUtil->activityLog($transaction, 'added');

            DB::commit();

            return response()->json([
                'success' => 1,
                'msg' => 'Sale completed',
                'transaction_id' => $transaction->id,
                'invoice_no' => $transaction->invoice_no,
                'receipt_url' => action([\App\Http\Controllers\SellController::class, 'show'], [$transaction->id]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().' Line:'.$e->getLine().' Message:'.$e->getMessage());

            return response()->json(['success' => 0, 'msg' => 'Something went wrong, sale not saved'], 500);
        }
    }
}
