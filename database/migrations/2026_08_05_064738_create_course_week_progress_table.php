<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_week_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_week_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('completed_items')->default(0);
            $table->unsignedTinyInteger('percent')->default(0);
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->unique(['course_enrollment_id', 'course_week_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_week_progress');
    }
};