<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_tran_agmt_prty', function (Blueprint $table) {
            $table->id('Agmt_Prty_UIN');

            // Agreement Reference
            $table->unsignedBigInteger('Agmt_UIN');

            // Party Information
            $table->string('Agmt_Prty_No', 255)->nullable();       // 1st Party, 2nd Party, 3rd Party
            $table->string('Agmt_Prty_Org_Name', 255)->nullable();
            $table->unsignedBigInteger('Orga_UIN')->nullable();
            $table->string('Agmt_Athr_Sgnt', 255)->nullable();

            // Audit Fields
            $table->unsignedBigInteger('CrBy')->nullable();
            $table->unsignedBigInteger('MoBy')->nullable();

            // Timestamps
            $table->timestamps();

            // Foreign Key
            $table->foreign('Agmt_UIN')
                  ->references('Agmt_UIN')
                  ->on('tbl_tran_agmt')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agreement_parties');
    }
};