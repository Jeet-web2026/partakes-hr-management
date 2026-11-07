<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_mast_intv_query_and_status_update', function (Blueprint $table) {
            $table->uuid('Ques_UIN')->change();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_mast_intv_query_and_status_update', function (Blueprint $table) {
            $table->unsignedBigInteger('Ques_UIN')->change();
        });
    }
};