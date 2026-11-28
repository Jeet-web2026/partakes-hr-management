<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Hr_mast_job_aply', function (Blueprint $table) {
            $table->string('Vr_Type')->nullable()->after('Vr_At');
        });
    }

    public function down(): void
    {
        Schema::table('Hr_mast_job_aply', function (Blueprint $table) {
            $table->dropColumn('Vr_Type');
        });
    }
};
