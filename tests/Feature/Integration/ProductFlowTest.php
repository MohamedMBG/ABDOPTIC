<?php

namespace Tests\Feature\Integration;

use App\Product;

class ProductFlowTest extends IntegrationTestCase
{
    public function test_create_single_product_persists_with_variation()
    {
        $name = 'Test Frame '.uniqid();
        $before = Product::where('business_id', 1)->count();

        $response = $this->actingAsAdmin()->post('/products', [
            'name' => $name,
            'unit_id' => 1,
            'type' => 'single',
            'barcode_type' => 'C128',
            'tax_type' => 'exclusive',
            'sku' => '',
            'single_dpp' => '10',
            'single_dpp_inc_tax' => '10',
            'profit_percent' => '100',
            'single_dsp' => '20',
            'single_dsp_inc_tax' => '20',
        ]);

        $this->assertContains($response->getStatusCode(), [200, 302]);
        $this->assertSame($before + 1, Product::where('business_id', 1)->count());

        $product = Product::where('business_id', 1)->where('name', $name)->first();
        $this->assertNotNull($product);
        // A single product must have exactly one variation row created for it.
        $this->assertSame(1, $product->variations()->count());
    }
}
