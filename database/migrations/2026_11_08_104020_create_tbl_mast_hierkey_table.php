<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_hierkey', function (Blueprint $table) {
            $table->id('Hiky_UIN');                              // PK
            $table->unsignedBigInteger('Orga_UIN');              // organisation / office reference
            $table->unsignedTinyInteger('Hiky_Levl');            // hierarchy level (1-7)
            $table->string('Hiky_Levl_Name', 100);               // e.g. Global Office, Branch Office
            $table->unsignedTinyInteger('Stau_UIN')->default(1); // status reference

            // Audit columns
            $table->unsignedBigInteger('CrBy');                  // created by
            $table->timestamp('CrOn')->useCurrent();             // created on
            $table->unsignedBigInteger('MoBy')->nullable();      // modified by
            $table->timestamp('MoOn')->nullable();               // modified on
            $table->unsignedBigInteger('DeBy')->nullable();      // deleted by
            $table->timestamp('DeOn')->nullable();               // deleted on

            $table->index('Orga_UIN');
            $table->index('Hiky_Levl');
            $table->index('Stau_UIN');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_hierkey');
    }
};
