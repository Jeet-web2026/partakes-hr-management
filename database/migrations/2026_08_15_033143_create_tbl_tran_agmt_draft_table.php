<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_tran_agmt_draft', function (Blueprint $table) {

            $table->id();

            // Organisation / User
            $table->string('Orga_UIN', 50)->nullable();
            $table->string('User_UIN', 50)->nullable();

            // Agreement details
            $table->string('Agmt_Catg_UIN', 50)->nullable();
            $table->string('Agmt_Type_UIN', 50)->nullable();
            $table->string('Agmt_Name', 255);

            // Draft version
            $table->string('Agmt_Vson', 50)->nullable();

            // Approval details
            $table->string('Agmt_Aprv_By', 50)->nullable();
            $table->date('Agmt_Aprv_Date')->nullable();

            // Uploaded files
            $table->string('Agmt_Drft_File_Name', 255)->nullable();
            $table->string('Agmt_Chklst_File_Name', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_tran_agmt_draft');
    }
};