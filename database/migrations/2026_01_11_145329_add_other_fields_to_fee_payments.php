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
        Schema::table('fee_payments', function (Blueprint $table) {
            //
            $table->string('reference_no')->nullable()->after('amount_paid'); // Transaction ID or Receipt number
            $table->text('remarks')->nullable()->after('reference_no')  ;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            //
            $table->dropColumn('reference_no');
            $table->dropColumn('remarks');
        });
    }
};
