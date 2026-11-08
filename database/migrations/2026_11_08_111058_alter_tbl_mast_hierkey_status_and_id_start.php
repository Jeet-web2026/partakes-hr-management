<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rows created with the old 1/0 status would break the foreign key
        DB::table('tbl_mast_hierkey')->update(['Stau_UIN' => 100201]);

        Schema::table('tbl_mast_hierkey', function (Blueprint $table) {
            $table->unsignedBigInteger('Stau_UIN')->default(100201)->change();
        });

        // Hiky_UIN numbering starts at 15000001 (MySQL)
        DB::statement('ALTER TABLE tbl_mast_hierkey AUTO_INCREMENT = 15000001');
    }

    public function down(): void
    {
        Schema::table('tbl_mast_hierkey', function (Blueprint $table) {
            $table->dropForeign(['Stau_UIN']);
        });
    }
};
