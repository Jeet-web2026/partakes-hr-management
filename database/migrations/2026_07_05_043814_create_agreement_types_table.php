<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_agmt_type', function (Blueprint $table) {
            // Primary Key
            $table->id('Agmt_Type_UIN');

            // Agreement Category
            $table->unsignedBigInteger('Agmt_Catg_UIN');

            // Agreement Type Name
            $table->string('Agmt_Type_Name', 255);

            // Status
            $table->unsignedBigInteger('Stau_UIN')->default(1);

            // Audit Fields
            $table->unsignedBigInteger('CrBy')->nullable();
            $table->unsignedBigInteger('MoBy')->nullable();

            // Timestamps
            $table->timestamps();

            // Index
            $table->index('Agmt_Catg_UIN', 'agreement_types_agmt_catg_uin_index');

            // Foreign Key
            $table->foreign('Agmt_Catg_UIN')
                ->references('Agmt_Catg_UIN')
                ->on('tbl_mast_agmt_catg')
                ->cascadeOnDelete();
                
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_agmt_type');
    }
};
