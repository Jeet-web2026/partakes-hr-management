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
        if (!Schema::hasColumn('HR_Mast_Job_Aply', 'IS_Vf')) {
            Schema::table('HR_Mast_Job_Aply', function (Blueprint $table) {
                $table->bigInteger('IS_Vf')->nullable();
                $table->bigInteger('Vr_By')->nullable();
                $table->timestamp('Vr_At')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('HR_Mast_Job_Aply', 'IS_Vf')) {
            Schema::table('HR_Mast_Job_Aply', function (Blueprint $table) {
                $table->dropColumn('IS_Vf');
                $table->dropColumn('Vr_By');
                $table->dropColumn('Vr_At');
            });
        }
    }
};
