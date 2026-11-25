<?php

use App\Models\EmplPosting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table((new EmplPosting)->getTable(), function (Blueprint $table) {
            $table->unsignedBigInteger('Aply_UIN')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table((new EmplPosting)->getTable(), function (Blueprint $table) {
            $table->dropUnique(['Aply_UIN']);
            $table->dropColumn('Aply_UIN');
        });
    }
};