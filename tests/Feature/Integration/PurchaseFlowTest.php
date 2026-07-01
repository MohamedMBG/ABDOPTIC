<?php

namespace Tests\Feature\Integration;

use App\Transaction;
use Illuminate\Support\Facades\DB;

/**
 * Exercises the purchase write path: PurchaseController@store ->
 * ProductUtil::createOrUpdatePurchaseLines / updateProductQuantity +
 * TransactionUtil payment & reference logic. The observable guarantee is
 * that a received purchase creates a purchase transaction and increases the
 * variation's stock by the purchased quantity.
 */
class PurchaseFlowTest extends IntegrationTestCase
{
    public function test_received_purchase_creates_transaction_and_increases_stock()
    {
        $business_id = 1;
        $location_id = 1;
        $supplier_id = 12;   // Luxottica Maroc (supplier)
        $product_id = 1;     // Test Optical Frame, enable_stock
        $variation_id = 1;
        $qty = 3;

        $before_txn = Transaction::where('business_id', $business_id)->where('type', 'purchase')->count();
        $before_stock = (float) DB::table('variation_location_details')
            ->where('variation_id', $variation_id)->where('location_id', $location_id)->value('qty_available');

        $response = $this->actingAsAdmin()->post('/purchases', [
            'status' => 'received',
            'contact_id' => $supplier_id,
            'transaction_date' => '06/30/2026 10:00',
            'location_id' => $location_id,
            'exchange_rate' => 1,
            'discount_type' => 'fixed',
            'discount_amount' => '0',
            'tax_id' => '',
            'tax_amount' => '0',
            'shipping_charges' => '0',
            'total_before_tax' => '300',
            'final_total' => '300',
            'payment' => [
                ['amount' => '300', 'method' => 'cash'],
            ],
            'purchases' => [
                [
                    'product_id' => $product_id,
                    'variation_id' => $variation_id,
                    'quantity' => $qty,
                    'pp_without_discount' => '100',
                    'discount_percent' => '0',
                    'purchase_price' => '100',
                    'purchase_price_inc_tax' => '100',
                    'item_tax' => '0',
                    'purchase_line_tax_id' => '',
                ],
            ],
        ]);

        $this->assertContains($response->getStatusCode(), [200, 302]);

        $this->assertSame(
            $before_txn + 1,
            Transaction::where('business_id', $business_id)->where('type', 'purchase')->count(),
            'a purchase transaction row should be created'
        );

        $after_stock = (float) DB::table('variation_location_details')
            ->where('variation_id', $variation_id)->where('location_id', $location_id)->value('qty_available');

        $this->assertEqualsWithDelta($before_stock + $qty, $after_stock, 0.0001, 'stock should rise by purchased qty');
    }
}
