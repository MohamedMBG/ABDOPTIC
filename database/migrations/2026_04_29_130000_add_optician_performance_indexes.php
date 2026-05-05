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
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->index(['business_id', 'contact_id', 'created_at'], 'prescriptions_business_contact_created_at_idx');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['business_id', 'type', 'optician_status'], 'transactions_business_type_optician_status_idx');
            $table->index(['business_id', 'prescription_id'], 'transactions_business_prescription_id_idx');
        });

        Schema::table('optician_status_histories', function (Blueprint $table) {
            $table->index(['transaction_id', 'created_at'], 'optician_status_histories_transaction_created_at_idx');
            $table->index(['created_by'], 'optician_status_histories_created_by_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropIndex('prescriptions_business_contact_created_at_idx');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_business_type_optician_status_idx');
            $table->dropIndex('transactions_business_prescription_id_idx');
        });

        Schema::table('optician_status_histories', function (Blueprint $table) {
            $table->dropIndex('optician_status_histories_transaction_created_at_idx');
            $table->dropIndex('optician_status_histories_created_by_idx');
        });
    }
};
