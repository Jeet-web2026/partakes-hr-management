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
        Schema::table('tbl_mast_intv_step', function (Blueprint $table) {
            $table->foreignId('Intv_Stage')
                ->nullable()
                ->after('Intv_Step_UIN')
                ->constrained('tbl_mast_intv_stage', 'id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_mast_intv_step', function (Blueprint $table) {
            $table->dropConstrainedForeignId('Intv_Stage');
        });
    }
};
