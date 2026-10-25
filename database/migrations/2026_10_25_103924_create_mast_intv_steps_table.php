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
        Schema::create('tbl_mast_intv_step', function (Blueprint $table) {
            $table->uuid('Intv_Step_UIN')->primary();
            $table->unsignedTinyInteger('Intv_Step_Numb');
            $table->string('Intv_Step_Name', 100);
            $table->enum('Intv_Step_Stau', ['active', 'inactive'])->default('active');
            $table->uuid('Orga_UIN');
            $table->uuid('Cr_By');
            $table->uuid('Mo_By');
            $table->uuid('Del_By')->nullable();
            $table->timestamp('Cr_On')->useCurrent();
            $table->timestamp('Mo_On')->useCurrent();

            $table->unique(['Orga_UIN', 'Intv_Step_Numb'], 'uq_intv_step_orga_numb');
            $table->unique(['Orga_UIN', 'Intv_Step_Name'], 'uq_intv_step_orga_name');
            $table->index('Orga_UIN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_intv_step');
    }
};
