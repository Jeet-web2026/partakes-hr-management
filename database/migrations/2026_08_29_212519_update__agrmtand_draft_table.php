<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_tran_agmt', function (Blueprint $table) {
            $table->timestamp('RenwOn')->nullable()->after('Agmt_Expy_Date');
            $table->timestamp('ExpOn')->nullable()->after('RenwOn');
            $table->enum('Agmt_Signed', ['yes', 'no'])->nullable()->after('ExpOn');
            $table->date('Signed_Recv_Date')->nullable()->after('Agmt_Signed');
            $table->enum('Agmt_Recv_Type', [
                'original_hard_copy',
                'duplicate_hard_copy',
                'pdf_copy',
            ])->nullable()->after('Signed_Recv_Date');
        });

        Schema::table('tbl_tran_agmt_draft', function (Blueprint $table) {
            $table->timestamp('CrOn')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_tran_agmt', function (Blueprint $table) {
            $table->dropColumn(['RenwOn', 'ExpOn', 'Agmt_Signed', 'Signed_Recv_Date', 'Agmt_Recv_Type']);
        });

        Schema::table('tbl_tran_agmt_draft', function (Blueprint $table) {
            $table->dropColumn('CrOn');
        });
    }
};
