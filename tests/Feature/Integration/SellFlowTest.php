<?php

namespace Tests\Feature\Integration;

use App\Transaction;
use Illuminate\Support\Facades\DB;

/**
 * Exercises the sell write path — the largest unit in the app:
 * SellPosController@store -> ProductUtil::calculateInvoiceTotal +
 * TransactionUtil::createSellTransaction / createOrUpdateSellLines +
 * stock decrement. is_direct_sale=1 bypasses the open-register requirement.
 * Observable guarantee: a final sale creates a sell transaction and lowers
 * the variation's stock by the sold quantity.
 */
class SellFlowTest extends IntegrationTestCase
{
    public function test_direct_final_sale_creates_transaction_and_decreases_stock()
    {
        $business_id = 1;
        $location_id = 1;
        $customer_id = 1;    // Walk-In Customer
        $product_id = 1;     // Test Optical Frame
        $variation_id = 1;
        $qty = 2;
        $price = 125;        // sell_price_inc_tax

        $before_txn = Transaction::where('business_id', $business_id)->where('type', 'sell')->count();
        $before_stock = (float) DB::table('variation_location_details')
            ->where('variation_id', $variation_id)->where('location_id', $location_id)->value('qty_available');

        $response = $this->actingAsAdmin()->post('/pos', [
            'is_direct_sale' => 1,
            'status' => 'final',
            'contact_id' => $customer_id,
            'location_id' => $location_id,
            'transaction_date' => '06/30/2026 10:00',
            'discount_type' => 'fixed',
            'discount_amount' => '0',
            'tax_rate_id' => '',
            'final_total' => (string) ($qty * $price),
            'products' => [
                [
                    'product_id' => $product_id,
                    'product_type' => 'single',
                    'variation_id' => $variation_id,
                    'quantity' => $qty,
                    'unit_price' => (string) $price,
                    'unit_price_inc_tax' => (string) $price,
                    'item_tax' => '0',
                    'tax_id' => '',
                    'enable_stock' => 1,
                ],
            ],
            'payment' => [
                [
                    'amount' => (string) ($qty * $price),
                    'method' => 'cash',
                ],
            ],
        ]);

        $this->assertContains($response->getStatusCode(), [200, 302]);

        $this->assertSame(
            $before_txn + 1,
            Transaction::where('business_id', $business_id)->where('type', 'sell')->count(),
            'a sell transaction row should be created'
        );

        $after_stock = (float) DB::table('variation_location_details')
            ->where('variation_id', $variation_id)->where('location_id', $location_id)->value('qty_available');

        $this->assertEqualsWithDelta($before_stock - $qty, $after_stock, 0.0001, 'stock should fall by sold qty');
    }
}
