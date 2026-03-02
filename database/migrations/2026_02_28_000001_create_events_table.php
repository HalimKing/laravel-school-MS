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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->unsignedBigInteger('user_id');

            // Event details
            $table->string('title');
            $table->longText('description');
            $table->string('location')->nullable();
            $table->string('category')->nullable(); // e.g., 'sports', 'academic', 'cultural', etc.

            // Dates and times
            $table->dateTime('event_date');
            $table->dateTime('event_end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Status
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('published');

            // Additional fields
            $table->text('notes')->nullable();
            $table->integer('attendance_count')->default(0);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('event_date');
            $table->index('status');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
