<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_empl_posting', function (Blueprint $table) {
            $table->bigIncrements('Post_UIN');
            $table->unsignedBigInteger('Orga_UIN')->nullable()->index();
            $table->unsignedBigInteger('User_UIN')->nullable()->index();
            $table->unsignedBigInteger('Ofic_UIN')->nullable()->index();
            $table->unsignedBigInteger('Dsig_UIN')->nullable()->index();
            $table->unsignedBigInteger('Dept_UIN')->nullable()->index();
            $table->unsignedBigInteger('Proj_UIN')->nullable()->index();
            $table->unsignedBigInteger('Rprt_Head_UIN')->nullable()->index();
            $table->date('Strt_Dt')->nullable();
            $table->date('End_Dt')->nullable();
            $table->unsignedBigInteger('Stau_UIN')->nullable()->index();
            $table->unsignedBigInteger('CrBy')->nullable();
            $table->timestamp('CrOn')->nullable();
            $table->unsignedBigInteger('MoBy')->nullable();
            $table->timestamp('MoOn')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_empl_posting');
    }
};
