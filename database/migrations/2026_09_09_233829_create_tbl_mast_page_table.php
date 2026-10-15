<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_page', function (Blueprint $table) {
            $table->bigIncrements('Page_UIN');

            $table->string('Page_name', 100);
            $table->string('Page_Desp', 100);

            $table->unsignedBigInteger('Dvlp_name');

            $table->integer('Data_Hit');
            $table->integer('Walt_Chag_Pont');
            $table->integer('Rate_Pont');

            $table->unsignedBigInteger('Data_Add');
            $table->unsignedBigInteger('Data_Updt');
            $table->unsignedBigInteger('Data_Del');
            $table->unsignedBigInteger('Data_VfBy');
            $table->unsignedBigInteger('Data_Adit');
            $table->unsignedBigInteger('Data_Prnt');
            $table->unsignedBigInteger('Data_Expt');
            $table->unsignedBigInteger('Data_Prnt_Scrn');
            $table->unsignedBigInteger('Data_Sele_Copy');
            $table->unsignedBigInteger('Data_Pste');
            $table->unsignedBigInteger('Data_Link');

            $table->unsignedBigInteger('CrBy');
            $table->unsignedBigInteger('CrOn');
            $table->unsignedBigInteger('MoBy');
            $table->unsignedBigInteger('MoOn');
            $table->unsignedBigInteger('VfBy');
            $table->unsignedBigInteger('VfOn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_page');
    }
};