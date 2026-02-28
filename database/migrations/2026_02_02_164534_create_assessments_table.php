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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->decimal('percentage', 5, 2);
            // Foreign Key: Linking to the Subjects table
            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            // Foreign Key: Linking to the Classes table
            $table->unsignedBigInteger('class_id');
            $table->foreign('class_id')
                  ->references('id')
                  ->on('class_models')
                  ->onDelete('cascade');
            
            $table->index(['subject_id', 'class_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
