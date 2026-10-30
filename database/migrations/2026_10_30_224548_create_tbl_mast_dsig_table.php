<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_dsig', function (Blueprint $table) {
            $table->bigIncrements('Dsig_UIN');
            $table->string('Dsig_Name', 150);
            $table->string('Dsig_Desp', 255)->nullable();
            $table->unsignedBigInteger('Dsig_Stau')->default(100201);
            $table->unsignedBigInteger('Org_UIN')->index();
            $table->unsignedBigInteger('CrBy')->nullable();
            $table->timestamp('CrOn')->nullable();
            $table->unsignedBigInteger('MoBy')->nullable();
            $table->timestamp('MoOn')->nullable();

            $table->foreign('Dsig_Stau')
                ->references('Stau_UIN')
                ->on('tbl_mast_stau');

            $table->unique(['Org_UIN', 'Dsig_Name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_dsig');
    }
};
