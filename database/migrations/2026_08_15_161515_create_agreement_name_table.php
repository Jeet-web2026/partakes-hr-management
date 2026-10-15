<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_agmt_name', function (Blueprint $table) {
            $table->id('Agmt_Name_UIN');
            $table->unsignedBigInteger('Agmt_Catg_UIN');
            $table->unsignedBigInteger('Agmt_Type_UIN');
            $table->unsignedBigInteger('Orga_UIN');
            $table->string('Agmt_Name');
            $table->string('Stau_UIN');

            $table->unsignedBigInteger('CrBy');
            $table->unsignedBigInteger('MoBy');
            $table->dateTime('CrOn');
            $table->dateTime('MoOn');

            $table->foreign('Agmt_Catg_UIN')->references('Agmt_Catg_UIN')->on('tbl_mast_agmt_catg')->cascadeOnDelete();
            $table->foreign('Agmt_Type_UIN')->references('Agmt_Type_UIN')->on('tbl_mast_agmt_type')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_agmt_name');
    }
};
