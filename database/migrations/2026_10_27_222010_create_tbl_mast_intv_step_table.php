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
        Schema::create('tbl_mast_intv_stage', function (Blueprint $table) {
            $table->id();
            $table->string('Stage_Name', 100)->unique();
            $table->boolean('Stage_Status')->default(true)->index();
            $table->uuid('Orga_UIN');
            $table->uuid('Cr_By');
            $table->uuid('Mo_By');
            $table->timestamp('Cr_On')->useCurrent();
            $table->timestamp('Mo_On')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_intv_stage');
    }
};
