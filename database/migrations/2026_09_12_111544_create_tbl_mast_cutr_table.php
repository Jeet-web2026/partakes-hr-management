<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_cutr', function (Blueprint $table) {
            $table->unsignedBigInteger('Admn_Cutr_Mast_UIN')->primary();
            $table->string('Name', 255);
            $table->string('Code', 10)->nullable();
            $table->string('Phon_Code', 10)->nullable();
            $table->string('Flag_Emoj', 10)->nullable();
            $table->smallInteger('MoNo_Digt');
            $table->bigInteger('Stau_UIN')->default(100201);
            $table->string('CrBy', 255)->nullable();
            $table->timestamp('CrOn')->nullable();
            $table->string('MoBy', 255)->nullable();
            $table->timestamp('MoOn')->nullable();
            $table->string('VfBy', 255)->nullable();
            $table->timestamp('VfOn')->nullable();
            $table->string('Del_By', 30)->nullable();
            $table->timestamp('Del_On')->nullable();
        });

        // The PK default (unix_timestamp() - 1592592000) can't be expressed via
        // Blueprint's fluent API, so it's applied as a raw ALTER after table creation.
        DB::statement("
            ALTER TABLE tbl_mast_cutr
            MODIFY Admn_Cutr_Mast_UIN BIGINT UNSIGNED NOT NULL
            DEFAULT (UNIX_TIMESTAMP() - 1592592000)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_cutr');
    }
};