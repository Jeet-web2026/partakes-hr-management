<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_dist', function (Blueprint $table) {
            $table->unsignedBigInteger('Dist_UIN')->primary();
            $table->unsignedBigInteger('Stat_UIN');
            $table->string('Dist_Comp_Name', 50);
            $table->string('Dist_Not_List', 20);
            $table->string('Dist_Shrt_Name', 12);
            $table->foreignId('Stau_UIN')
                ->nullable()
                ->constrained('tbl_mast_stau', 'Stau_UIN')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedBigInteger('CrBy');
            $table->date('CrOn')->nullable();
            $table->unsignedBigInteger('MoBy')->nullable();
            $table->date('MoOn')->nullable();
            $table->unsignedBigInteger('VfBy')->nullable();
            $table->date('VfOn')->nullable();
            $table->unsignedInteger('Del_BY')->nullable();
            $table->date('Del_On')->nullable();

            $table->index('Stat_UIN');
            $table->foreign('Stat_UIN')
                ->references('Stat_UIN')->on('tbl_mast_stat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_dist');
    }
};
