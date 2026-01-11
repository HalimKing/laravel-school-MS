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
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('level_data_id');
            $table->foreign('level_data_id')->references('id')->on('level_data')->onDelete('cascade');
            $table->unsignedBigInteger('fee_id');
            $table->foreign('fee_id')->references('id')->on('fees')->onDelete('cascade');
            $table->string('payment_method');
            $table->date('payment_date');
            $table->decimal('amount_paid', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};
