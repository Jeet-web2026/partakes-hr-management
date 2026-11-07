<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_intv_query_and_status_update', function (Blueprint $table) {
            $table->id('Qury_UIN');
            $table->unsignedBigInteger('Org_UIN')->index();
            $table->unsignedBigInteger('Appl_UIN')->index();      // candidate application
            $table->unsignedBigInteger('Stage_UIN')->index();     // Stage
            $table->unsignedBigInteger('Intv_Step_UIN')->index(); // Steps
            $table->boolean('Qury_Selected')->default(false);     // Flags > Selected
            $table->unsignedBigInteger('Ques_UIN')->index();      // Question
            $table->text('Qury_Answer');                          // Answer
            $table->decimal('Qury_Marks', 5, 2);                  // Marks

            $table->unsignedBigInteger('CrBy')->nullable();
            $table->timestamp('CrOn')->nullable();
            $table->unsignedBigInteger('MoBy')->nullable();
            $table->timestamp('MoOn')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_intv_query_and_status_update');
    }
};
