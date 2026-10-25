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
        Schema::create('tbl_mast_hr_flag', function (Blueprint $table) {
            $table->uuid('hr_flag_UIN')->primary();
            $table->string('hr_flag_stge_name', 100);   // fixed stage name (HrFlagStage enum)
            $table->string('hr_flag_name', 100);
            $table->enum('hr_flag_stau', ['active', 'inactive'])->default('active');
            $table->uuid('Orga_UIN');
            $table->uuid('Cr_By');
            $table->uuid('Mo_By');
            $table->uuid('Del_By')->nullable(); // soft delete marker (NULL = not deleted)
            $table->timestamp('Cr_On')->useCurrent();
            $table->timestamp('Mo_On')->useCurrent();

            $table->index(['Orga_UIN', 'hr_flag_stge_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mast_hr_flags');
    }
};
