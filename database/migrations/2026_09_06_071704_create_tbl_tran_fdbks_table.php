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
        Schema::create('tbl_tran_fdbk', function (Blueprint $table) {
            $table->id('Fdbk_UIN');
            $table->unsignedBigInteger('Orga_UIN');
            $table->unsignedBigInteger('Page_UIN');
            $table->string('Page_load_speed', 20);
            $table->string('Page_Dsin', 20);
            $table->string('Page_Ease_Of_Use', 20);
            $table->string('Page_Oall', 20);
            $table->string('Page_InfoClarity', 20);
            $table->string('Fdbk_Type', 100);
            $table->string('Lokn_for', 100);
            $table->string('Rcmd_Page', 100);
            $table->string('Cnta_Us', 100);
            $table->text('Mesg_Fdbk');
            $table->integer('PinCod');
            $table->unsignedBigInteger('CrBy');
            $table->integer('CrOn');
            $table->text('Fdbk_Attachment')->nullable();

            $table->index('Orga_UIN');
            $table->index('Page_UIN');
            $table->index('CrBy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_tran_fdbk');
    }
};
