<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tran_agmt_remk', function (Blueprint $table) {
            $table->integer('agmt_remk_uin', true); // primary key, auto-increment
            $table->integer('agmt_uin');
            $table->date('agmt_remk_date');
            $table->string('agmt_remk', 5000);
            $table->integer('stau_uin');
            $table->integer('crby');
            
            $table->integer('moby');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tran_agmt_remk');
    }
};
