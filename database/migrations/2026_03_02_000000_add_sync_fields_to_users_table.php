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
        Schema::table('users', function (Blueprint $table) {
            // Add fields to track synced data
            $table->string('syncable_type')->nullable()->comment('Model type: Teacher, Guardian, Student, etc.');
            $table->unsignedBigInteger('syncable_id')->nullable()->comment('ID from the syncable table');
            $table->boolean('synced')->default(false)->comment('Whether this user was synced');;

            // Add unique constraint for sync tracking
            $table->index(['syncable_type', 'syncable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['syncable_type', 'syncable_id']);
            $table->dropColumn(['syncable_type', 'syncable_id', 'synced']);
        });
    }
};
