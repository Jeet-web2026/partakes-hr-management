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
            $table->integer('Agmt_Size_KB')->nullable()->after('Agmt_Atch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_tran_agmt', function (Blueprint $table) {
            $table->dropColumn('Agmt_Size_KB');
        });
    }
};
