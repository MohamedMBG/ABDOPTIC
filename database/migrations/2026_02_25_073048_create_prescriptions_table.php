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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('contact_id');
            $table->string('od_sphere')->nullable();
            $table->string('od_cylinder')->nullable();
            $table->string('od_axis')->nullable();
            $table->string('od_addition')->nullable();
            $table->string('od_prism')->nullable();
            $table->string('od_base')->nullable();
            $table->string('od_pd')->nullable();
            $table->string('os_sphere')->nullable();
            $table->string('os_cylinder')->nullable();
            $table->string('os_axis')->nullable();
            $table->string('os_addition')->nullable();
            $table->string('os_prism')->nullable();
            $table->string('os_base')->nullable();
            $table->string('os_pd')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            // Contact foreign key relationship (in Ultimate POS contacts id is integer, unsigned)
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prescriptions');
    }
};
