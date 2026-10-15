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
        Schema::create('tbl_mast_pin_code', function (Blueprint $table) {
            // Surrogate primary key. NOTE: Pin_code_UIN is NOT unique in the
            // source data — the same pincode UIN legitimately repeats across
            // multiple districts/locality types (confirmed: 1,478 duplicate
            // UINs out of 21,161 rows in the original dump), so it can't be
            // the primary key despite the *_UIN naming convention.

            // Kept as a plain indexed column, not unique.
            $table->unsignedInteger('Pin_code_UIN');

            // The actual pin code number, e.g. 744301
            $table->unsignedInteger('pin_code_No');

            // Foreign UIN references (country / state / district masters)
            $table->unsignedInteger('Cutr_UIN')->index();
            $table->unsignedInteger('Stat_UIN')->index();
            $table->unsignedInteger('Dist_UIN')->index();

            // Rural / Urban / Metro
            $table->string('Loca_Type', 15);

            // Status master UIN
            $table->unsignedInteger('Stau_UIN')->index();

            // Audit columns (project convention: no MoOn on this table, CrOn only)
            $table->unsignedInteger('CrBy');
            $table->unsignedInteger('CrOn');

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_pin_code');
    }
};
