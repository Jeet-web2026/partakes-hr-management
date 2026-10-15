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
        Schema::create('tbl_mast_orga', function (Blueprint $table) {
            // Primary key (assumed auto-increment; adjust if not)
            $table->bigIncrements('Orga_UIN');

            $table->bigInteger('Orga_Type_UIN');
            $table->string('Orga_Name', 30);
            $table->string('orga_Logo', 100);

            $table->bigInteger('Dt_of_Incp');
            $table->bigInteger('Fnal_Year_Strt_Dt');
            $table->bigInteger('Actn_Strt_Dt');

            $table->string('Web_Addr', 20);
            $table->text('Regi_Addr')->nullable();

            $table->bigInteger('Cnta_Pers');
            $table->bigInteger('Admt_Pers_1st');
            $table->bigInteger('Admt_Pers_2nd');
            $table->bigInteger('Admt_Pers_3rd');
            $table->bigInteger('Admt_Pers_4th');
            $table->bigInteger('Assc_Part_Code');
            $table->bigInteger('Sytm_Admt_Pers');

            $table->dateTime('Eft_Dt');
            $table->bigInteger('Renw_Dt');
            $table->bigInteger('Expy_Dt');
            $table->bigInteger('Orga_Del_Dt');

            $table->bigInteger('Stau_UIN');
            $table->bigInteger('CrBy');
            $table->bigInteger('CrOn');
            $table->bigInteger('MoBy')->nullable();
            $table->bigInteger('MoOn')->nullable();
            $table->bigInteger('VfOn')->nullable();
            $table->bigInteger('VfBy')->nullable();

            $table->bigInteger('Stat_UIN');
            $table->string('pin_UIN', 10);

            $table->string('Prmy_Emai', 64);
            $table->bigInteger('Prmy_Emai_Vefi');

            $table->integer('mpos_key');

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
        Schema::dropIfExists('tbl_mast_orga');
    }
};
