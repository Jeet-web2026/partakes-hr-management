<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_mast_dsig', function (Blueprint $table) {
            $table->unsignedBigInteger('Parn_Dsig_UIN')->default(0)->after('Dsig_Type')->index(); // 0 = Primary Designation
        });
    }

    public function down(): void
    {
        Schema::table('tbl_mast_dsig', function (Blueprint $table) {
            $table->dropColumn('Parn_Dsig_UIN');
        });
    }
};