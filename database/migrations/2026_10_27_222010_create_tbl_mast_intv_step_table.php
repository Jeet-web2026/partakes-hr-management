<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_mast_intv_stage', function (Blueprint $table) {
            $table->id('Stage_UIN');
            $table->string('Stage_Name', 100);
            $table->integer('Stage_Sort')->unsigned()->default(0);
            $table->foreignId('Stage_Status')
                ->nullable()
                ->constrained('tbl_mast_stau', 'Stau_UIN')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->uuid('Orga_UIN');
            $table->uuid('Cr_By');
            $table->uuid('Mo_By');
            $table->timestamp('Cr_On')->useCurrent();
            $table->timestamp('Mo_On')->useCurrent();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE tbl_mast_intv_stage AUTO_INCREMENT = 1001');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_intv_stage');
    }
};
