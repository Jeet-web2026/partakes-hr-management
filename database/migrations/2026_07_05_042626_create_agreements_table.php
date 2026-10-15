<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_tran_agmt', function (Blueprint $table) {
            // Primary Key
            $table->id('Agmt_UIN'); // BIGINT UNSIGNED AUTO_INCREMENT

            $table->string('Agmt_File_no', 100)->nullable();

            // Agreement Details
            $table->string('Agmt_Catg_UIN', 255);
            $table->string('Agmt_Type_UIN', 255);
            $table->string('Agmt_Name', 255);

            // Dates
            $table->dateTime('Agmt_Strt_Date')->nullable();
            $table->dateTime('Agmt_Expy_Date')->nullable();
            $table->dateTime('Agmt_Sign_Date')->nullable();
            $table->dateTime('Agmt_Ntry_Date')->nullable();

            // Additional Information
            $table->string('Agmt_Vson', 255)->nullable();
            $table->string('Agmt_Drft_By', 255)->nullable();
            $table->string('Agmt_Judn', 255)->nullable();
            $table->string('Agmt_Atch', 255)->nullable();
            $table->text('Agmt_Remk')->nullable();

            // Parent Agreement (self-reference, for renewal/versioning)
            $table->unsignedBigInteger('Agmt_Parn_UIN')->nullable();

            // Counter-party link (no FK constraint defined)
            $table->unsignedBigInteger('Agmt_Prty_UIN')->nullable();

            // Organization & User
            $table->unsignedBigInteger('Orga_UIN');
            $table->unsignedBigInteger('User_UIN');

            // Audit Fields
            $table->unsignedBigInteger('CrBy')->nullable();
            $table->unsignedBigInteger('MoBy')->nullable();

            // Status
            $table->enum('Stau_UIN', ['active', 'expired', 'renewed'])
                  ->default('active');

            // Update tracking
            $table->unsignedInteger('update_count')
                  ->default(0)
                  ->comment("When a agreeemnt updated count > 2; [remarks hide]");

            // Laravel timestamps
            $table->timestamps();

            // Foreign Keys
            $table->foreign('Agmt_Parn_UIN', 'tbl_tran_agmt_agmt_parn_uin_foreign')
                  ->references('Agmt_UIN')
                  ->on('tbl_tran_agmt')
                  ->nullOnDelete();

            $table->foreign('Orga_UIN')
                  ->references('Orga_UIN')
                  ->on('tbl_mast_orga')
                  ->cascadeOnDelete();

            $table->foreign('User_UIN')
                  ->references('User_UIN')
                  ->on('tbl_mast_user_logi')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_tran_agmt');
    }
};