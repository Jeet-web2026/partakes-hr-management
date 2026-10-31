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
        Schema::table('hr_mast_job_aply', function (Blueprint $table) {
            if (! Schema::hasColumn('hr_mast_job_aply', 'Distr')) {
                $table->string('Distr', 100)->nullable()->after('PinCod_UIN');
            }
            if (! Schema::hasColumn('hr_mast_job_aply', 'State')) {
                $table->string('State', 100)->nullable()->after('PinCod_UIN');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_mast_job_aply', function (Blueprint $table) {
            $table->dropColumn(['Distr', 'State']);
        });
    }
};
