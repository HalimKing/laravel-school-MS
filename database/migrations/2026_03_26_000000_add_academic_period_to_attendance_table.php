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
        Schema::table('attendance', function (Blueprint $table) {
            // Add academic_period_id after academic_year_id
            $table->foreignId('academic_period_id')->nullable()->after('academic_year_id')->constrained('academic_periods')->cascadeOnDelete();

            // Add index for faster queries
            $table->index('academic_period_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['academic_period_id']);
            $table->dropIndexIfExists(['academic_period_id']);
            $table->dropColumn('academic_period_id');
        });
    }
};
