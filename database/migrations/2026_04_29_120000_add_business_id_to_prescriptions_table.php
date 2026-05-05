<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->unsignedInteger('business_id')->nullable()->after('id')->index();
        });

        DB::table('prescriptions')
            ->join('contacts', 'contacts.id', '=', 'prescriptions.contact_id')
            ->whereNull('prescriptions.business_id')
            ->update([
                'prescriptions.business_id' => DB::raw('contacts.business_id'),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropIndex(['business_id']);
            $table->dropColumn('business_id');
        });
    }
};
