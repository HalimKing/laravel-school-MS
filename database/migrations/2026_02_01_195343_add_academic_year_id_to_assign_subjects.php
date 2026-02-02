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
        Schema::table('assign_subjects', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('academic_year_id')->after('class_id')->nullable();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assign_subjects', function (Blueprint $table) {
            //
            $table->dropForeign(['academic_year_id']);
        });
    }
};
