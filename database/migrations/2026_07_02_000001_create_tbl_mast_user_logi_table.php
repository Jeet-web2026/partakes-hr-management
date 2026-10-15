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
        Schema::create('tbl_mast_user_logi', function (Blueprint $table) {
            // Primary key (assumed auto-increment; adjust if not)
            $table->bigIncrements('User_UIN');

            $table->string('User_Name', 50);
            $table->string('User_Logo', 100);

            $table->string('Prmy_Emai', 64);
            $table->bigInteger('Prmy_Emai_Vefi')->nullable();
            $table->string('Altn_Emai', 64);
            $table->bigInteger('Altn_Emai_Vefi')->nullable();

            $table->bigInteger('Cutr_UIN');
            $table->bigInteger('ISD_UIN');

            $table->bigInteger('Prmy_MoNo');
            $table->bigInteger('Prmy_MoNo_Vefi');
            $table->integer('OTP_Prmy_MoNo')->nullable();

            $table->bigInteger('Altn_MoNo')->nullable();
            $table->bigInteger('Altn_MoNo_Vefi')->nullable();
            $table->integer('OTP_Altn_MoNo')->nullable();

            $table->string('User_pasw', 256);
            $table->integer('Pasw_Self_Gntd')->nullable();
            $table->bigInteger('Pasw_Rest_By')->nullable();
            $table->bigInteger('Pasw_Rest_On')->nullable();

            $table->bigInteger('CrBy')->nullable();
            $table->bigInteger('Stau_UIN')->nullable();

            // Match original table collation/charset
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_user_logi');
    }
};
