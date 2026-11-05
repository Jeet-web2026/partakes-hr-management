<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_stau', function (Blueprint $table) {
            // UINs are assigned manually (2, 101, 100201...), so no auto-increment
            $table->unsignedBigInteger('Stau_UIN')->primary();
            $table->unsignedBigInteger('Orga_UIN')->index();
            $table->string('Stau_Name', 100);
            $table->string('Stau_Desp', 255)->nullable();
            $table->unsignedBigInteger('CrBy')->default(0);
            $table->unsignedBigInteger('MoBy')->default(0);
            $table->timestamp('MoOn')->nullable();
            $table->timestamp('VfOn')->nullable();
            $table->unsignedBigInteger('VfBy')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_stau');
    }
};
