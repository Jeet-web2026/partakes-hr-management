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
            $table->enum('Mo_Type', ['Smart Phone', 'Featured Phone', 'Satellite Phone'])->nullable()->change();
            $table->enum('MO_OS', ['Android', 'iOS'])->nullable()->change();
            $table->enum('Lptp_OS', ['Android', 'iOS', 'Windows'])->nullable()->change();
            $table->enum('App_Stus', ['new', 'shortlisted', 'interview_scheduled', 'selected', 'offer_letter_issued', 'accepted']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
