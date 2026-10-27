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
        Schema::table('tbl_mast_hr_flag', function (Blueprint $table) {
            $table->string('hr_flag_placeholder')->nullable()->after('hr_flag_name');
            $table->string('hr_flag_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_mast_hr_flag', function (Blueprint $table) {
            $table->dropColumn('hr_flag_placeholder');
        });
    }
};
