<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_mast_dept', function (Blueprint $table) {
            $table->charset('latin1');
            $table->collation('latin1_swedish_ci');

            $table->bigInteger('Dept_UIN')->primary();
            $table->bigInteger('Orga_UIN');
            $table->string('Dept_name', 20);
            $table->bigInteger('parn_Dept_id');
            $table->integer('Chld_grup');
            $table->integer('ldgr');
            $table->bigInteger('Stau_UIN');
            $table->bigInteger('CrBy');
            $table->bigInteger('CrOn');
            $table->bigInteger('MoBy')->nullable();
            $table->bigInteger('MoOn')->nullable();
            $table->bigInteger('VfOn')->nullable();
            $table->bigInteger('VfBy')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_mast_dept');
    }
};
