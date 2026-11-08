<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_office', function (Blueprint $table) {
            $table->id('Ofic_UIN');                                   // PK
            $table->unsignedBigInteger('Parn_Ofic_UIN')->nullable();  // parent office (self reference)
            $table->unsignedBigInteger('Hiky_UIN');                   // hierkey (decides office level)
            $table->unsignedBigInteger('Orga_UIN');                   // organisation

            $table->string('Ofic_Code', 50)->unique();
            $table->string('Ofic_Name', 150);

            // Location references
            $table->unsignedBigInteger('Cutr_UIN')->nullable();       // country
            $table->unsignedBigInteger('Stat_UIN')->nullable();       // state
            $table->unsignedBigInteger('Dist_UIN')->nullable();       // district
            $table->unsignedBigInteger('PinCod_UIN')->nullable();     // pincode
            $table->unsignedBigInteger('Loca_UIN')->nullable();       // locality
            $table->unsignedBigInteger('Stet_UIN')->nullable();       // street

            $table->string('Bild_No_Name', 150)->nullable();          // building no. / name

            // Office details
            $table->date('Ofic_Strt_Dt')->nullable();                 // start date
            $table->string('Ofic_LdLi', 20)->nullable();              // landline
            $table->string('Ofic_Emai', 150)->nullable();             // email
            $table->date('Ofic_Clos_Dt')->nullable();                 // close date

            $table->decimal('Lon', 10, 7)->nullable();
            $table->decimal('Lat', 10, 7)->nullable();

            $table->unsignedTinyInteger('Stau_UIN')->default(1);      // record status

            // Audit columns
            $table->unsignedBigInteger('CrBy');
            $table->timestamp('CrOn')->useCurrent();
            $table->unsignedBigInteger('MoBy')->nullable();
            $table->timestamp('MoOn')->nullable();
            $table->unsignedBigInteger('DeBy')->nullable();
            $table->timestamp('DeOn')->nullable();

            // Indexes
            $table->index('Parn_Ofic_UIN');
            $table->index('Hiky_UIN');
            $table->index('Orga_UIN');
            $table->index('Cutr_UIN');
            $table->index('Stat_UIN');
            $table->index('Dist_UIN');
            $table->index('PinCod_UIN');
            $table->index('Stau_UIN');

            // Foreign keys (only where the parent table is known)
            $table->foreign('Parn_Ofic_UIN')->references('Ofic_UIN')->on('tbl_mast_office');
            $table->foreign('Hiky_UIN')->references('Hiky_UIN')->on('tbl_mast_hierkey');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_office');
    }
};
