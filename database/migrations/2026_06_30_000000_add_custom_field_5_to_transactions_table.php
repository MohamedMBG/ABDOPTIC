<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * createSellTransaction() writes transactions.custom_field_5, but the column was never
 * added — so every sale (POS checkout included) failed with "Unknown column custom_field_5".
 * This adds the missing column to match custom_field_1..4.
 */
return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('transactions', 'custom_field_5')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('custom_field_5')->nullable()->after('custom_field_4');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('transactions', 'custom_field_5')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('custom_field_5');
            });
        }
    }
};
