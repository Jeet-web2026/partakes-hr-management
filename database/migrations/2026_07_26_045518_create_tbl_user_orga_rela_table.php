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
        Schema::create('tbl_user_orga_rela', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('Orga_User_Assc_UIN');
            $table->unsignedBigInteger('Orga_UIN');
            $table->unsignedInteger('Vert_UIN')->default(0);
            $table->unsignedBigInteger('User_UIN');
            $table->timestamp('Expy')->nullable();
            $table->unsignedBigInteger('Stau_UIN')->nullable();
            $table->timestamp('CrOn')->nullable();
            $table->string('CrBy', 255)->nullable();
            $table->timestamp('MoOn')->nullable();
            $table->string('MoBy', 255)->nullable();

            $table->index('Orga_UIN');
            $table->index('User_UIN');
            $table->index('Stau_UIN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_user_orga_rela');
    }
};
