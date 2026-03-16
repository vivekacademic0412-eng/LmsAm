<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_enrollment_id')->constrained('course_enrollments')->cascadeOnDelete();
            $table->foreignId('course_day_item_id')->constrained('course_day_items')->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['course_enrollment_id', 'course_day_item_id'], 'unique_enrollment_item_progress');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_progress');
    }
};
