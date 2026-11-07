<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('HR_Aply_Flag', function (Blueprint $table) {
            $table->unsignedBigInteger('Aply_UIN');
            $table->uuid('hr_flag_UIN');

            $table->primary(['Aply_UIN', 'hr_flag_UIN']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('HR_Aply_Flag');
    }
};
