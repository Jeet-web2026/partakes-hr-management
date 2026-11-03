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
            if (! Schema::hasColumn('HR_Mast_Job_Aply', 'Distr')) {
                $table->string('Distr', 100)->nullable()->after('PinCod_UIN');
            }
            if (! Schema::hasColumn('HR_Mast_Job_Aply', 'State')) {
                $table->string('State', 100)->nullable()->after('PinCod_UIN');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('HR_Mast_Job_Aply', function (Blueprint $table) {
            $table->dropColumn(['Distr', 'State']);
        });
    }
};
