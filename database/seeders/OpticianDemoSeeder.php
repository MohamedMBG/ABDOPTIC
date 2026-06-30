<?php

namespace Database\Seeders;

use App\Brands;
use App\Category;
use App\Product;
use App\ProductVariation;
use App\Utils\ProductUtil;
use App\VariationLocationDetails;
use Illuminate\Database\Seeder;

/**
 * Realistic optician demo catalogue for the barcode-scan POS.
 *
 * Each product gets a scannable barcode (variation sub_sku), price, stock and
 * optical details — including deliberate low-stock and out-of-stock items so the
 * green/orange/red badges can be seen. Re-runnable: existing barcodes are skipped.
 *
 * Run:  php artisan db:seed --class=OpticianDemoSeeder
 */
class OpticianDemoSeeder extends Seeder
{
    public function run()
    {
        $productUtil = app(ProductUtil::class);

        $business_id = 1;
        $location_id = 1;
        $unit_id = 1;
        $user_id = 1;

        $brand = fn ($name) => Brands::firstOrCreate(
            ['business_id' => $business_id, 'name' => $name],
            ['created_by' => $user_id]
        )->id;

        $category = fn ($name) => Category::firstOrCreate(
            ['business_id' => $business_id, 'name' => $name, 'category_type' => 'product'],
            ['created_by' => $user_id, 'parent_id' => 0]
        )->id;

        // name, brand, category, barcode, price, stock, min_stock, optical[]
        $items = [
            // --- Sunglasses (frames) ---
            ['Ray-Ban Aviator RB3025', 'Ray-Ban', 'Sunglasses', '6001234500011', 1200, 12, 4,
                ['optical_product_type' => 'frame', 'frame_color' => 'Gold', 'frame_eye_size' => '58', 'frame_bridge_size' => '14', 'frame_temple_length' => '135']],
            ['Ray-Ban Wayfarer RB2140', 'Ray-Ban', 'Sunglasses', '6001234500028', 1100, 8, 4,
                ['optical_product_type' => 'frame', 'frame_color' => 'Black', 'frame_eye_size' => '50', 'frame_bridge_size' => '22', 'frame_temple_length' => '150']],
            ['Oakley Holbrook', 'Oakley', 'Sunglasses', '6001234500035', 1500, 3, 5,   // low stock
                ['optical_product_type' => 'frame', 'frame_color' => 'Matte Black', 'frame_eye_size' => '55', 'frame_bridge_size' => '18', 'frame_temple_length' => '137']],
            ['Persol PO0649', 'Persol', 'Sunglasses', '6001234500042', 1800, 0, 3,     // out of stock
                ['optical_product_type' => 'frame', 'frame_color' => 'Havana', 'frame_eye_size' => '54', 'frame_bridge_size' => '20', 'frame_temple_length' => '140']],
            ['Gucci GG0061S', 'Gucci', 'Sunglasses', '6001234500059', 2400, 5, 3,
                ['optical_product_type' => 'frame', 'frame_color' => 'Tortoise', 'frame_eye_size' => '57', 'frame_bridge_size' => '16', 'frame_temple_length' => '140']],

            // --- Optical frames ---
            ['Ray-Ban Clubmaster RX5154', 'Ray-Ban', 'Frames', '6001234500066', 950, 10, 4,
                ['optical_product_type' => 'frame', 'frame_color' => 'Black/Gold', 'frame_eye_size' => '51', 'frame_bridge_size' => '21', 'frame_temple_length' => '145']],
            ['Silhouette Titan Minimal', 'Silhouette', 'Frames', '6001234500073', 2200, 6, 3,
                ['optical_product_type' => 'frame', 'frame_color' => 'Gunmetal', 'frame_eye_size' => '53', 'frame_bridge_size' => '19', 'frame_temple_length' => '140']],
            ['Tom Ford FT5634-B', 'Tom Ford', 'Frames', '6001234500080', 1900, 2, 3,   // low stock
                ['optical_product_type' => 'frame', 'frame_color' => 'Dark Havana', 'frame_eye_size' => '54', 'frame_bridge_size' => '17', 'frame_temple_length' => '145']],

            // --- Lenses ---
            ['Essilor Varilux X Progressive', 'Essilor', 'Lenses', '6001234500097', 1600, 25, 8,
                ['optical_product_type' => 'lens', 'lens_type' => 'Progressive', 'lens_material' => 'Plastic', 'lens_index' => '1.67', 'lens_coating' => 'Crizal Sapphire']],
            ['Zeiss SmartLife Single Vision', 'Zeiss', 'Lenses', '6001234500103', 700, 40, 10,
                ['optical_product_type' => 'lens', 'lens_type' => 'Single Vision', 'lens_material' => 'Plastic', 'lens_index' => '1.50', 'lens_coating' => 'DuraVision Platinum']],
            ['Zeiss DuraVision BlueProtect 1.6', 'Zeiss', 'Lenses', '6001234500110', 900, 4, 6,  // low stock
                ['optical_product_type' => 'lens', 'lens_type' => 'Single Vision', 'lens_material' => 'Plastic', 'lens_index' => '1.60', 'lens_coating' => 'BlueProtect']],

            // --- Contact lenses ---
            ['Acuvue Oasys (6 pack)', 'Acuvue', 'Contact Lenses', '6001234500127', 350, 30, 10,
                ['optical_product_type' => 'contact_lens', 'lens_type' => 'Bi-weekly', 'lens_material' => 'Senofilcon A']],
            ['Acuvue 1-Day Moist (30 pack)', 'Acuvue', 'Contact Lenses', '6001234500134', 400, 18, 8,
                ['optical_product_type' => 'contact_lens', 'lens_type' => 'Daily', 'lens_material' => 'Etafilcon A']],
            ['Biotrue ONEday (30 pack)', 'Bausch+Lomb', 'Contact Lenses', '6001234500141', 380, 0, 8,  // out of stock
                ['optical_product_type' => 'contact_lens', 'lens_type' => 'Daily', 'lens_material' => 'Nesofilcon A']],

            // --- Accessories ---
            ['Lens Cleaning Spray 50ml', 'Optic Care', 'Accessories', '6001234500158', 80, 60, 15,
                ['optical_product_type' => 'other']],
            ['Microfiber Cleaning Cloth', 'Optic Care', 'Accessories', '6001234500165', 30, 100, 20,
                ['optical_product_type' => 'other']],
            ['Hard Glasses Case', 'Optic Care', 'Accessories', '6001234500172', 120, 45, 10,
                ['optical_product_type' => 'other']],
        ];

        $created = 0;
        $skipped = 0;
        $sku_seq = 1000;

        foreach ($items as [$name, $brand_name, $cat_name, $barcode, $price, $stock, $min, $optical]) {
            // Skip if this barcode already exists (re-runnable).
            if (\App\Variation::where('sub_sku', $barcode)->exists()) {
                $skipped++;
                continue;
            }

            $product = Product::create(array_merge([
                'name' => $name,
                'business_id' => $business_id,
                'type' => 'single',
                'unit_id' => $unit_id,
                'brand_id' => $brand($brand_name),
                'category_id' => $category($cat_name),
                'sku' => 'OPT-'.(++$sku_seq),
                'barcode_type' => 'C128',
                'enable_stock' => 1,
                'alert_quantity' => $min,
                'tax_type' => 'exclusive',
                'is_inactive' => 0,
                'not_for_selling' => 0,
                'created_by' => $user_id,
            ], $optical));

            // Variation = the scannable barcode (sub_sku). Tax 0 -> sell == sell_inc_tax.
            $productUtil->createSingleProductVariation(
                $product, $barcode, $price * 0.6, $price * 0.6, 40, $price, $price
            );

            // Make the product available at the location.
            $product->product_locations()->sync([$location_id]);

            // Opening stock for this location.
            $variation = $product->variations()->first();
            VariationLocationDetails::create([
                'product_id' => $product->id,
                'product_variation_id' => $variation->product_variation_id,
                'variation_id' => $variation->id,
                'location_id' => $location_id,
                'qty_available' => $stock,
            ]);

            $created++;
        }

        $this->command->info("Optician demo: {$created} products created, {$skipped} skipped (already existed).");
    }
}
