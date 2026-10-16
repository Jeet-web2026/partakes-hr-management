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
        Schema::table('HR_Mast_Job_Aply', function (Blueprint $table) {
            $table->unsignedBigInteger('Link_UIN')->nullable()->index()->after('Aply_UIN');
            $table->unsignedBigInteger('Orga_UIN')->nullable()->index()->after('Link_UIN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('HR_Mast_Job_Aply', function (Blueprint $table) {
            $table->dropColumn(['Link_UIN', 'Orga_UIN']);
        });
    }
};
