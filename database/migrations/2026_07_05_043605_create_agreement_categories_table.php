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
        Schema::create('tbl_mast_agmt_catg', function (Blueprint $table) {
            $table->id('Agmt_Catg_UIN');

            // Category Name
            $table->string('Agmt_Catg_Name', 255);

            // Parent Category (Self Reference)
            $table->unsignedBigInteger('Agmt_Parn_Catg_UIN')->nullable();

            // Status
            $table->unsignedBigInteger('Stau_UIN')->default(1);

            // Audit Fields
            $table->unsignedBigInteger('CrBy')->nullable();
            $table->unsignedBigInteger('MoBy')->nullable();

            // Timestamps
            $table->timestamps();

            // Self Foreign Key
            $table->foreign('Agmt_Parn_Catg_UIN')
                ->references('Agmt_Catg_UIN')
                ->on('tbl_mast_agmt_catg')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_agmt_catg');
    }
};
