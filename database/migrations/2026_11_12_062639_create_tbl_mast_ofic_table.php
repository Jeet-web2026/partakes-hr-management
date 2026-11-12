<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_ofic', function (Blueprint $table) {
            $table->id('Ofic_UIN');                                   // 25000001...
            $table->unsignedBigInteger('Parn_Ofic_UIN')->default(0)->index(); // 0 = top level
            $table->unsignedBigInteger('Hiky_UIN')->index();          // office type
            $table->unsignedBigInteger('Orga_UIN')->index();
            $table->string('Ofic_Code', 20)->unique();
            $table->string('Ofic_Name');

            $table->unsignedBigInteger('Cutr_UIN')->nullable();
            $table->unsignedBigInteger('Stat_UIN')->nullable();
            $table->unsignedBigInteger('Dist_UIN')->nullable();
            $table->string('PinCod_UIN', 20)->nullable();
            $table->string('Loca_UIN')->nullable();
            $table->string('Stet_UIN')->nullable();
            $table->string('Bild_No_Name')->nullable();

            $table->date('Ofic_Strt_Dt')->nullable();
            $table->date('Ofic_Clos_Dt')->nullable();
            $table->string('Ofic_LdLi', 30)->nullable();
            $table->string('Ofic_Emai')->nullable();
            $table->decimal('Lon', 11, 8)->nullable();
            $table->decimal('Lat', 10, 8)->nullable();
            $table->unsignedBigInteger('Stau_UIN')->nullable()->index();

            $table->unsignedBigInteger('CrBy')->nullable();
            $table->timestamp('CrOn')->nullable();
            $table->unsignedBigInteger('MoBy')->nullable();
            $table->timestamp('MoOn')->nullable();
            $table->unsignedBigInteger('DeBy')->nullable();
            $table->timestamp('DeOn')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_ofic');
    }
};
