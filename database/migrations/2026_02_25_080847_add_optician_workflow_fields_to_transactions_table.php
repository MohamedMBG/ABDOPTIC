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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('optician_status')->nullable()->comment('prescription_received, lenses_ordered, in_assembly, ready_for_pickup, delivered');
            $table->unsignedInteger('prescription_id')->nullable()->comment('Link to specific prescription used for this order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('optician_status');
            $table->dropColumn('prescription_id');
        });
    }
};
