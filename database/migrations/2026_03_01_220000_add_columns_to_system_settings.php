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
        // Check if columns don't already exist
        if (Schema::hasTable('system_settings')) {
            Schema::table('system_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('system_settings', 'key')) {
                    $table->string('key')->unique()->after('id');
                }
                if (!Schema::hasColumn('system_settings', 'value')) {
                    $table->longText('value')->nullable()->after('key');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['key', 'value']);
        });
    }
};
