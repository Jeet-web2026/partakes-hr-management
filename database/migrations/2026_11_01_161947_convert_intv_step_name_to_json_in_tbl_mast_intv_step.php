<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $table = 'tbl_mast_intv_step';

    public function up(): void
    {
        // remove leftovers from the failed run + the old string columns
        foreach (['Intv_Step_Name_Json', 'Intv_Step_Name', 'Intv_Step_Placeholder_Type'] as $column) {
            if (Schema::hasColumn($this->table, $column)) {
                Schema::table($this->table, fn(Blueprint $table) => $table->dropColumn($column));
            }
        }

        Schema::table($this->table, function (Blueprint $table) {
            $table->text('Intv_Step_Name')->nullable()->after('Intv_Step_UIN');
            $table->text('Intv_Step_Placeholder_Type')->nullable()->after('Intv_Step_Name');
        });
    }

    public function down(): void
    {
        foreach (['Intv_Step_Name', 'Intv_Step_Placeholder_Type'] as $column) {
            if (Schema::hasColumn($this->table, $column)) {
                Schema::table($this->table, fn(Blueprint $table) => $table->dropColumn($column));
            }
        }

        Schema::table($this->table, function (Blueprint $table) {
            $table->string('Intv_Step_Name', 100)->after('Intv_Step_UIN');
        });
    }
};
