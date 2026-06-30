<?php

namespace Database\Seeders;

use App\Contact;
use App\Product;
use App\PurchaseLine;
use App\Transaction;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\VariationLocationDetails;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Operational demo data on top of the catalogue (OpticianDemoSeeder):
 *   - opening stock (purchase lines) so sales map to real inventory
 *   - customers + suppliers
 *   - completed POS sales over the last weeks (real transactions, decrement stock)
 *
 * Sales reuse the same engine as the live checkout, with user_id passed explicitly
 * (a seeder has no auth() user). Re-runnable: skips work already done.
 *
 * Run:  php artisan db:seed --class=OpticianFullDataSeeder
 */
class OpticianFullDataSeeder extends Seeder
{
    public function run()
    {
        $productUtil = app(ProductUtil::class);
        $transactionUtil = app(TransactionUtil::class);
        $businessUtil = app(BusinessUtil::class);

        $business_id = 1;
        $location_id = 1;
        $user_id = 1;

        // ---------- 1. Opening stock (purchase lines for the demo catalogue) ----------
        $demo_products = Product::where('business_id', $business_id)
            ->where('sku', 'like', 'OPT-%')
            ->where('enable_stock', 1)
            ->get();

        $opening = 0;
        foreach ($demo_products as $product) {
            $variation = $product->variations()->first();
            if (! $variation) {
                continue;
            }

            // Already has a purchase line? skip (re-runnable).
            if (PurchaseLine::where('variation_id', $variation->id)->exists()) {
                continue;
            }

            $qty = (float) VariationLocationDetails::where('variation_id', $variation->id)
                ->where('location_id', $location_id)->value('qty_available');
            // Opening stock should reflect what was originally received, not the live qty.
            // Use live qty + a buffer so there is purchase history to map sales against.
            $opening_qty = max($qty, 1) + 10;
            $cost = (float) $variation->default_purchase_price;

            $ot = Transaction::create([
                'business_id' => $business_id,
                'location_id' => $location_id,
                'type' => 'opening_stock',
                'opening_stock_product_id' => $product->id,
                'status' => 'received',
                'payment_status' => 'paid',
                'contact_id' => null,
                'transaction_date' => \Carbon::now()->subDays(90),
                'total_before_tax' => $cost * $opening_qty,
                'final_total' => $cost * $opening_qty,
                'created_by' => $user_id,
            ]);

            $pl = new PurchaseLine();
            $pl->transaction_id = $ot->id;
            $pl->product_id = $product->id;
            $pl->variation_id = $variation->id;
            $pl->quantity = $opening_qty;
            $pl->pp_without_discount = $cost;
            $pl->purchase_price = $cost;
            $pl->purchase_price_inc_tax = $cost;
            $pl->item_tax = 0;
            $pl->quantity_sold = 0;
            $pl->quantity_adjusted = 0;
            $pl->quantity_returned = 0;
            $pl->mfg_quantity_used = 0;
            $pl->save();

            // Keep live stock as the catalogue defined it: opening was qty+10, so sell off 10
            // on paper by marking them sold, leaving quantity_available == catalogue qty.
            $pl->quantity_sold = $opening_qty - $qty;
            $pl->save();

            $opening++;
        }

        // ---------- 2. Customers ----------
        $customers = [
            ['Youssef El Amrani', '0612345601', 'youssef.amrani@example.ma', 'Casablanca'],
            ['Fatima Zahra Benali', '0612345602', 'fatima.benali@example.ma', 'Rabat'],
            ['Omar Tazi', '0612345603', 'omar.tazi@example.ma', 'Marrakech'],
            ['Salma Idrissi', '0612345604', 'salma.idrissi@example.ma', 'Fès'],
            ['Mehdi Cherkaoui', '0612345605', 'mehdi.cherkaoui@example.ma', 'Tanger'],
            ['Khadija Alaoui', '0612345606', 'khadija.alaoui@example.ma', 'Agadir'],
            ['Hamza Bennani', '0612345607', 'hamza.bennani@example.ma', 'Casablanca'],
            ['Nadia Saidi', '0612345608', 'nadia.saidi@example.ma', 'Oujda'],
        ];

        $customer_ids = [];
        $seq = (int) (Contact::where('business_id', $business_id)->max('id') ?? 0);
        foreach ($customers as [$name, $mobile, $email, $city]) {
            $c = Contact::firstOrCreate(
                ['business_id' => $business_id, 'mobile' => $mobile],
                [
                    'type' => 'customer',
                    'contact_type' => 'individual',
                    'name' => $name,
                    'first_name' => $name,
                    'email' => $email,
                    'city' => $city,
                    'country' => 'Morocco',
                    'contact_id' => 'CO'.str_pad(++$seq, 4, '0', STR_PAD_LEFT),
                    'contact_status' => 'active',
                    'created_by' => $user_id,
                    'credit_limit' => 0,
                ]
            );
            $customer_ids[] = $c->id;
        }

        // ---------- 3. Suppliers ----------
        $suppliers = [
            ['Luxottica Maroc', 'Luxottica Group', '0522110011'],
            ['Essilor Maroc', 'EssilorLuxottica', '0522110022'],
            ['CooperVision Distribution', 'CooperVision', '0522110033'],
        ];
        foreach ($suppliers as [$name, $company, $mobile]) {
            Contact::firstOrCreate(
                ['business_id' => $business_id, 'mobile' => $mobile],
                [
                    'type' => 'supplier',
                    'contact_type' => 'business',
                    'supplier_business_name' => $company,
                    'name' => $name,
                    'first_name' => $name,
                    'contact_id' => 'SU'.str_pad(++$seq, 4, '0', STR_PAD_LEFT),
                    'contact_status' => 'active',
                    'created_by' => $user_id,
                ]
            );
        }

        // ---------- 4. Completed sales ----------
        // Only sell items that are actually in stock right now.
        $sellable = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
            ->join('variation_location_details as vld', 'vld.variation_id', '=', 'variations.id')
            ->where('p.sku', 'like', 'OPT-%')
            ->where('vld.location_id', $location_id)
            ->where('vld.qty_available', '>', 2)
            ->select('variations.id as variation_id', 'p.id as product_id',
                'variations.sell_price_inc_tax as price', 'vld.qty_available')
            ->get()->all();

        $sales = 0;
        if (! empty($sellable)) {
            // 14 sales spread over the last 6 weeks (deterministic, no rand()).
            for ($i = 0; $i < 14; $i++) {
                $line_count = ($i % 3) + 1; // 1..3 items
                $cart = [];
                for ($j = 0; $j < $line_count; $j++) {
                    $pick = $sellable[($i * 3 + $j) % count($sellable)];
                    $cart[] = ['variation_id' => $pick->variation_id, 'product_id' => $pick->product_id,
                        'price' => (float) $pick->price, 'quantity' => ($j % 2) + 1];
                }
                $date = \Carbon::now()->subDays(42 - $i * 3)->setTime(10 + ($i % 8), ($i * 7) % 60);
                $contact_id = $customer_ids[$i % count($customer_ids)];

                if ($this->finalizeSale($productUtil, $transactionUtil, $businessUtil,
                    $business_id, $location_id, $user_id, $contact_id, $cart, $date)) {
                    $sales++;
                }
            }
        }

        $this->command->info("Full data: {$opening} opening-stock entries, ".count($customer_ids)." customers, "
            .count($suppliers)." suppliers, {$sales} sales.");
    }

    /**
     * Finalise one sale through the real engine (mirror of BarcodeScanController@checkout,
     * with explicit user_id/date because a seeder has no auth() user or request).
     */
    private function finalizeSale($productUtil, $transactionUtil, $businessUtil,
        $business_id, $location_id, $user_id, $contact_id, $cart, $date)
    {
        try {
            DB::beginTransaction();

            $products = [];
            foreach ($cart as $line) {
                // Don't oversell: clamp to what's available.
                $available = (float) VariationLocationDetails::where('variation_id', $line['variation_id'])
                    ->where('location_id', $location_id)->value('qty_available');
                $qty = min($line['quantity'], max(0, $available - 1));
                if ($qty <= 0) {
                    continue;
                }
                $products[] = [
                    'product_id' => $line['product_id'],
                    'variation_id' => $line['variation_id'],
                    'product_type' => 'single',
                    'enable_stock' => 1,
                    'quantity' => $qty,
                    'unit_price' => $line['price'],
                    'unit_price_inc_tax' => $line['price'],
                    'item_tax' => 0,
                    'tax_id' => null,
                    'base_unit_multiplier' => 1,
                    'sell_line_note' => '',
                ];
            }
            if (empty($products)) {
                DB::rollBack();
                return false;
            }

            $discount = ['discount_type' => 'fixed', 'discount_amount' => 0];
            $invoice_total = $productUtil->calculateInvoiceTotal($products, null, $discount, false);

            $input = [
                'business_id' => $business_id,
                'location_id' => $location_id,
                'contact_id' => $contact_id,
                'transaction_date' => $date,
                'status' => 'final',
                'is_quotation' => 0,
                'discount_type' => 'fixed',
                'discount_amount' => 0,
                'tax_rate_id' => null,
                'tax_amount' => 0,
                'final_total' => $invoice_total['final_total'],
                'sale_note' => null,
                'shipping_charges' => 0,
                'products' => $products,
            ];

            $transaction = $transactionUtil->createSellTransaction($business_id, $input, $invoice_total, $user_id, false);
            $transactionUtil->createOrUpdateSellLines($transaction, $products, $location_id, false, null, [], false);

            $payment = [[
                'amount' => $transaction->final_total,
                'method' => 'cash',
                'paid_on' => $date->toDateTimeString(),
                'is_return' => 0,
            ]];
            $transactionUtil->createOrUpdatePaymentLines($transaction, $payment, $business_id, $user_id, false);

            foreach ($products as $p) {
                $productUtil->decreaseProductQuantity($p['product_id'], $p['variation_id'], $location_id, $p['quantity']);
            }

            $transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            $business = ['id' => $business_id, 'accounting_method' => 'fifo',
                'location_id' => $location_id, 'pos_settings' => []];
            $transactionUtil->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->warn('Sale skipped: '.$e->getMessage());
            return false;
        }
    }
}
