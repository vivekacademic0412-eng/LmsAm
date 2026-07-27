<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('min_completion_percent')->default(80);

            // sequential_all_weeks = Basic/Diploma, week1_gate_only = Professional, free_no_lock = Crash Course
            $table->enum('weekly_unlock_mode', ['sequential_all_weeks', 'week1_gate_only', 'free_no_lock'])
                ->default('sequential_all_weeks');

            $table->enum('certificate_mode', ['end_of_course', 'per_week', 'per_level', 'both'])
                ->default('end_of_course');

            // Advanced level: always show seats as full, never actually purchasable
            $table->boolean('show_seats_as_full')->default(false);
            $table->unsignedInteger('total_seats')->nullable();
            $table->unsignedInteger('booked_seats')->nullable();

            // Day-0 countdown before course access starts
            $table->boolean('zero_day_countdown_enabled')->default(true);
            $table->unsignedInteger('countdown_days')->default(0);

            $table->timestamps();
            $table->unique('course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_settings');
    }
};