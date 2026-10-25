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
        Schema::create('tbl_mast_intv_qstn', function (Blueprint $table) {
            $table->uuid('intv_qstn_UIN')->primary();
            $table->text('Question_Text');
            $table->enum('Intv_qstn_Stau', ['active', 'inactive'])->default('active');
            $table->uuid('Orga_UIN');
            $table->uuid('Cr_By');
            $table->uuid('Mo_By');
            $table->uuid('Del_By')->nullable(); // soft delete marker (NULL = not deleted)
            $table->timestamp('Cr_On')->useCurrent();
            $table->timestamp('Mo_On')->useCurrent();

            $table->index('Orga_UIN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mast_intv_qstns');
    }
};
