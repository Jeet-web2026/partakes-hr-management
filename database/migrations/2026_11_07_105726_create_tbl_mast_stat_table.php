<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_stat', function (Blueprint $table) {
            $table->unsignedBigInteger('Stat_UIN')->primary();
            $table->unsignedBigInteger('Cutr_UIN');
            $table->string('Stat_Comp_Name', 50);
            $table->string('Stat_Type', 20);
            $table->string('Stat_Shrt_Name', 5);
            $table->unsignedBigInteger('City_UIN');
            $table->foreignId('Stau_UIN')
                ->nullable()
                ->constrained('tbl_mast_stau', 'Stau_UIN')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedBigInteger('CrBy');
            $table->unsignedBigInteger('MoBy')->nullable();
            $table->timestamp('MoOn')->nullable();
            $table->timestamp('VfOn')->nullable();
            $table->unsignedBigInteger('VfBy')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_stat');
    }
};
