<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Transaction;
use App\Utils\TransactionUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\ContactUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Product;
use App\Variation;
use App\ProductVariation;
use App\BusinessLocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class ManualSellController extends Controller
{
    protected $transactionUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $contactUtil;

    public function __construct(
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil,
        ProductUtil $productUtil,
        ContactUtil $contactUtil
    ) {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->contactUtil = $contactUtil;
    }

    public function index()
    {
        return redirect()->action([ManualSellController::class, 'create']);
    }

    public function create()
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $walk_in_customer = Contact::where('business_id', $business_id)
                            ->where('type', 'customer')
                            ->where('is_default', 1)
                            ->first();

        return view('manual_invoice.form')->with(compact('walk_in_customer'));
    }

    public function store(Request $request)
    {
        // Force English locale for consistent number formatting
        App::setLocale('en');
        setlocale(LC_NUMERIC, 'C');

        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        // Validate input data
        $validatedData = $request->validate([
            'transaction_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.quantity' => 'required|numeric|min:1',
            'contact_id' => 'nullable|exists:contacts,id',
            'customer_name' => 'required_if:contact_id,null|string|max:255',
            'mobile' => 'nullable|string|max:20'
        ]);

        try {
            $input = $request->except('_token');
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            DB::beginTransaction();

            // Handle Customer
            $contact_id = $this->handleCustomer($input, $business_id, $user_id);

            // Get or create manual product
            $manual_product = $this->getOrCreateManualProduct($business_id);

            // Get default location
            $default_location = BusinessLocation::where('business_id', $business_id)->first();
            if (empty($default_location)) {
                throw new \Exception("No business location found");
            }

            // Process transaction data
            $transaction_data = $this->prepareTransactionData(
                $input,
                $business_id,
                $user_id,
                $contact_id,
                $default_location->id
            );

            // Create transaction
            $transaction = $this->transactionUtil->createSellTransaction(
                $business_id,
                $transaction_data,
                [
                    'total_before_tax' => $transaction_data['total_before_tax'],
                    'tax' => 0
                ],
                $user_id,
                false
            );

            // Add sell lines
            $this->addSellLines($transaction, $input['products'], $manual_product);

            DB::commit();

            return $this->handleSuccessResponse($request);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            return $this->handleErrorResponse($request, $e);
        }
    }

    protected function handleCustomer($input, $business_id, $user_id)
    {
        $contact_id = $input['contact_id'] ?? null;
        $customer_name = trim($input['customer_name'] ?? '');
        $customer_mobile = trim($input['mobile'] ?? '');
        $customer_address = trim($input['address'] ?? '');

        if (empty($contact_id)) {
            if (!empty($customer_name)) {
                $contact = Contact::where('business_id', $business_id)
                                  ->where('name', $customer_name);
                if (!empty($customer_mobile)) {
                    $contact = $contact->where('mobile', $customer_mobile);
                }
                $contact = $contact->first();

                if ($contact) {
                    return $contact->id;
                }

                // Create new contact
                $ref_count = $this->contactUtil->setAndGetReferenceCount('customer', $business_id);
                $contact_ref_id = $this->contactUtil->generateReferenceNumber('customer', $ref_count, $business_id);

                return Contact::create([
                    'business_id' => $business_id,
                    'type' => 'customer',
                    'name' => $customer_name,
                    'mobile' => $customer_mobile,
                    'address_line_1' => $customer_address,
                    'created_by' => $user_id,
                    'contact_id' => $contact_ref_id
                ])->id;
            }

            // Fallback to default customer
            $default_customer = Contact::where('business_id', $business_id)
                                    ->where('type', 'customer')
                                    ->where('is_default', 1)
                                    ->first();
            if ($default_customer) {
                return $default_customer->id;
            }

            throw new \Exception("Customer name is required if not selecting an existing customer.");
        }

        return $contact_id;
    }

    protected function prepareTransactionData($input, $business_id, $user_id, $contact_id, $location_id)
{
    // Calculate total
    $final_total = collect($input['products'])->sum(function($product) {
        $quantity = $this->transactionUtil->num_uf($product['quantity'] ?? 1);
        $price = $this->transactionUtil->num_uf($product['price'] ?? 0);
        return $quantity * $price;
    });

    // Handle discount - default to 0 if not provided
    $discount_amount = $this->transactionUtil->num_uf($input['discount_amount'] ?? 0);
    $discount_type = $input['discount_type'] ?? 'percentage';
    
    // Apply discount
    if ($discount_type == 'percentage' && $discount_amount > 0) {
        $final_total = $final_total * (1 - ($discount_amount/100));
    } elseif ($discount_type == 'fixed') {
        $final_total = $final_total - $discount_amount;
    }

    // Format transaction date
    $transaction_date = !empty($input['transaction_date'])
        ? Carbon::parse($input['transaction_date'])
        : now();

    return [
        'business_id' => $business_id,
        'location_id' => $location_id,
        'type' => 'sell',
        'status' => 'final',
        'contact_id' => $contact_id,
        'invoice_no' => $input['invoice_number'] ?? null,
        'transaction_date' => $transaction_date,
        'total_before_tax' => $final_total,
        'final_total' => $final_total,
        'discount_amount' => $discount_amount,
        'discount_type' => $discount_type,
        'created_by' => $user_id,
        'custom_field_1' => json_encode([
            'od_measurement' => $input['od_measurement'] ?? null,
            'og_measurement' => $input['og_measurement'] ?? null,
            'midar' => $input['midar'] ?? null
        ]),
        'is_direct_sale' => 1,
        'is_quotation' => 0,
        'shipping_charges' => 0,
        'exchange_rate' => 1,
        'additional_notes' => $input['additional_notes'] ?? null
    ];
}

    protected function addSellLines($transaction, $products, $manual_product)
    {
        $variation = Variation::where('product_id', $manual_product->id)->first();

        $sell_lines = collect($products)->map(function($product) use ($transaction, $manual_product, $variation) {
            return [
                'transaction_id' => $transaction->id,
                'product_id' => $manual_product->id,
                'variation_id' => $variation->id,
                'quantity' => $this->transactionUtil->num_uf($product['quantity'] ?? 1),
                'unit_price' => $this->transactionUtil->num_uf($product['price'] ?? 0),
                'unit_price_inc_tax' => $this->transactionUtil->num_uf($product['price'] ?? 0),
                'item_tax' => 0,
                'sell_line_note' => $product['name'] ?? 'Manual Product',
                'created_at' => now(),
                'updated_at' => now()
            ];
        })->toArray();

        DB::table('transaction_sell_lines')->insert($sell_lines);
    }

    protected function handleSuccessResponse($request)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'msg' => __('sale.sell_created_success'),
                'redirect_url' => action([\App\Http\Controllers\SellController::class, 'index'])
            ]);
        }

        return redirect('sells')->with('status', [
            'success' => 1, 
            'msg' => __('sale.sell_created_success')
        ]);
    }

    protected function handleErrorResponse($request, $exception)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong') . ' ' . $exception->getMessage()
            ]);
        }

        return back()->with('status', [
            'success' => 0, 
            'msg' => __('messages.something_went_wrong')
        ]);
    }
    private function getOrCreateManualProduct($business_id)
    {
        // Check if a manual product already exists
        $manual_product = Product::where('business_id', $business_id)
            ->where('name', 'Manual Invoice Product')
            ->first();

        if (!empty($manual_product)) {
            return $manual_product;
        }

        // Create a new manual product
        DB::beginTransaction();
        try {
            // Get default unit
            $unit_id = DB::table('units')
                        ->where('business_id', $business_id)
                        ->where('is_default', 1)
                        ->value('id');
            
            if (empty($unit_id)) {
                $unit_id = DB::table('units')
                            ->where('business_id', $business_id)
                            ->value('id');
            }
            
            if (empty($unit_id)) {
                // Create a default unit if none exists
                $unit_id = DB::table('units')->insertGetId([
                    'business_id' => $business_id,
                    'name' => 'Pieces',
                    'short_name' => 'Pc',
                    'allow_decimal' => 0,
                    'created_by' => auth()->user()->id,
                    'is_default' => 1
                ]);
            }

            // Create the product
            $product_data = [
                'name' => 'Manual Invoice Product',
                'business_id' => $business_id,
                'type' => 'single',
                'unit_id' => $unit_id,
                'tax_type' => 'none',
                'enable_stock' => 0,
                'is_inactive' => 0,
                'created_by' => auth()->user()->id
            ];

            $manual_product = Product::create($product_data);

            // Create a product variation
            $product_variation = ProductVariation::create([
                'name' => 'DUMMY',
                'product_id' => $manual_product->id,
                'is_dummy' => 1
            ]);

            // Create a variation
            Variation::create([
                'name' => 'DUMMY',
                'product_id' => $manual_product->id,
                'product_variation_id' => $product_variation->id,
                'default_purchase_price' => 0,
                'dpp_inc_tax' => 0,
                'profit_percent' => 0,
                'default_sell_price' => 0,
                'sell_price_inc_tax' => 0
            ]);

            DB::commit();
            return $manual_product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get the next invoice number.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoiceNumber()
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $ref_count = $this->transactionUtil->setAndGetReferenceCount('sell');
        $invoice_no = $this->transactionUtil->generateReferenceNumber('sell', $ref_count);

        return response()->json(['invoice_number' => $invoice_no]);
    }
}

