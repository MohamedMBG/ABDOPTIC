<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('optical_product_type')->nullable()->comment('frame, lens, contact_lens, other');
            $table->string('frame_color')->nullable();
            $table->string('frame_eye_size')->nullable();
            $table->string('frame_bridge_size')->nullable();
            $table->string('frame_temple_length')->nullable();
            
            $table->string('lens_material')->nullable();
            $table->string('lens_coating')->nullable();
            $table->string('lens_type')->nullable();
            $table->string('lens_index')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'optical_product_type',
                'frame_color',
                'frame_eye_size',
                'frame_bridge_size',
                'frame_temple_length',
                'lens_material',
                'lens_coating',
                'lens_type',
                'lens_index'
            ]);
        });
    }
};
