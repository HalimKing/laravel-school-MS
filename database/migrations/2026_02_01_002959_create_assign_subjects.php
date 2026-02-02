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
        Schema::create('assign_subjects', function (Blueprint $table) {
           $table->id();
            // Foreign key to the teachers table (usually users with a specific role)
            $table->foreignId('teacher_id')
                  ->constrained('teachers')
                  ->onDelete('cascade');

            // Foreign key to the subjects table
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->onDelete('cascade');

           
            // class foreign key
            $table->foreignId('class_id')
                  ->constrained('class_models')
                  ->onDelete('cascade');
          
            $table->timestamps();
            $table->softDeletes(); // For historical record keeping

            // Unique constraint to prevent duplicate assignments for the same slot
            $table->index(['teacher_id', 'subject_id', 'class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_subjects');
    }
};
