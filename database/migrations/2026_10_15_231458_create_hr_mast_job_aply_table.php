<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('HR_Mast_Job_Aply', function (Blueprint $table) {
            $table->id('Aply_UIN');

            // Personal
            $table->string('FaNm', 100)->nullable();
            $table->string('MiNm', 100)->nullable();
            $table->string('SuNm', 100)->nullable();
            $table->string('Gend', 10)->nullable();
            $table->date('DOB')->nullable();
            $table->string('MoTu', 10)->nullable();
            $table->unsignedBigInteger('Cutr_UIN')->nullable();
            $table->string('PinCod_UIN', 10)->nullable();

            // Contact
            $table->string('Mo_No', 15)->nullable();
            $table->string('Emai')->nullable();

            // Mobile
            $table->boolean('Own_Mo')->default(false);
            $table->enum('Mo_Type', ['smartphone', 'keypad'])->nullable();
            $table->enum('MO_OS', ['android', 'ios', 'other'])->nullable();

            // Laptop
            $table->boolean('Own_Lptp')->default(false);
            $table->enum('Lptp_OS', ['windows', 'macos', 'linux', 'other'])->nullable();

            // Driving licence
            $table->boolean('Drvi_Lisc')->default(false);
            $table->string('Vcle_Type_Lisc', 100)->nullable();

            // Relocation
            $table->boolean('Rlca')->default(false);
            $table->string('Rlca_Area')->nullable();

            // Job
            $table->string('Job_Intr', 255);
            $table->string('Job_Type', 50)->nullable();
            $table->string('Bio_Data_Path', 500)->nullable();

            // Timestamps (custom column names)
            $table->timestamp('CrBy')->nullable();
            $table->timestamp('UpBy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('HR_Mast_Job_Aply');
    }
};
