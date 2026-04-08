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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('level_data_id');
            $table->foreign('level_data_id')->references('id')->on('level_data')->cascadeOnDelete();
            $table->unsignedBigInteger('class_id');
            $table->foreign('class_id')->references('id')->on('class_models')->cascadeOnDelete();
            $table->unsignedBigInteger('academic_year_id');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->foreign('teacher_id')->references('id')->on('teachers')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->index('student_id');
            $table->index('class_id');
            $table->index('academic_year_id');
            $table->index('attendance_date');
            $table->unique(
                ['student_id', 'level_data_id', 'attendance_date', 'subject_id'],
                'attendance_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
