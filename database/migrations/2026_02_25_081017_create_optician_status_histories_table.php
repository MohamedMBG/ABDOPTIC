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
        Schema::create('optician_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('transaction_id')->comment('The order/sale ID');
            $table->string('status');
            $table->text('notes')->nullable();
            $table->unsignedInteger('created_by');
            $table->boolean('customer_notified')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('optician_status_histories');
    }
};
