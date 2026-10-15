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
        Schema::create('tbl_mast_sbcr_log_book', function (Blueprint $table) {
            $table->unsignedBigInteger('Log_book_uin')->primary();
            $table->unsignedBigInteger('Sbcr_Rvnu_Catg_Name_UIN');
            $table->string('Indv_Orga', 100);
            $table->unsignedBigInteger('Indv_Orga_Uin');
            $table->string('Cont', 255)->nullable();
            $table->string('UOM', 50)->nullable();
            $table->decimal('Size', 10, 2)->nullable();
            $table->unsignedBigInteger('Vert_Uin');
            $table->string('Desc', 500)->nullable();
            $table->unsignedBigInteger('User_UIN');
            $table->timestamp('CrOn')->useCurrent();

            $table->index('Sbcr_Rvnu_Catg_Name_UIN');
            $table->index('User_UIN');
            $table->index('Indv_Orga_Uin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_sbcr_log_book');
    }
};
