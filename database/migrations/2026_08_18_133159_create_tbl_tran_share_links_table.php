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
        Schema::create('tbl_tran_share_links', function (Blueprint $table) {
            $table->unsignedBigInteger('Link_UIN');
            $table->unsignedBigInteger('Link_Foreign_Id_UIN');
            $table->string('Link_Token', 64)->unique();
            $table->unsignedBigInteger('CrBy');
            $table->unsignedBigInteger('MoBy');
            $table->dateTime('CrOn');
            $table->dateTime('MoOn');
            $table->string('Link_Type')->index();
            $table->dateTime('Link_Expy_On');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_tran_share_links');
    }
};
