<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tran_agmt_remk', function (Blueprint $table) {
            $table->text('CrBy')->nullable()->change();
            $table->text('MoBy')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tran_agmt_remk', function (Blueprint $table) {
            $table->integer('CrBy')->nullable()->change();
            $table->integer('MoBy')->nullable()->change();
        });
    }
};
