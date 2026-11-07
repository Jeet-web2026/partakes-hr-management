<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_mast_dsig', function (Blueprint $table) {
            $table->string('Dsig_Type', 20)->nullable()->after('Dsig_Desp');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_mast_dsig', function (Blueprint $table) {
            $table->dropColumn('Dsig_Type');
        });
    }
};