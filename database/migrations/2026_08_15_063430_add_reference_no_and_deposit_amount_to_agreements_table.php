<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_tran_agmt', function (Blueprint $table) {
            $table->string('Agmt_Ref_No', 30)
                ->nullable()
                ->after('Agmt_UIN')
                ->comment('System-generated traceable reference number, visible on edit/view');

            $table->decimal('Agmt_Depst_Amt', 15, 2)
                ->nullable()
                ->after('Agmt_Remk')
                ->comment('Security deposit / bond amount taken from business partner');

            $table->unique(['Orga_UIN', 'Agmt_Ref_No'], 'agmt_orga_ref_no_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_tran_agmt', function (Blueprint $table) {
            $table->dropUnique('agmt_orga_ref_no_unique');
            $table->dropColumn(['Agmt_Ref_No', 'Agmt_Depst_Amt']);
        });
    }
};