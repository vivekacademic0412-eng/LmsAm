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
        Schema::create('course_type_settings', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('course_type_id')->constrained()->cascadeOnDelete();
 
            // "Zero day" grace period — days between registration and when
            // day-1 of the course clock actually starts (your "user 21
            // ko register hua toh 7 din baad zero day start" rule).
            $table->unsignedInteger('exception_days')->default(0);
 
            // % of the course that must be watched before the course
            // certificate unlocks (80 for Basic/Diploma, 75-80 for Professional).
            $table->unsignedTinyInteger('completion_threshold_percent')->default(80);
 
            // % of an individual session/video that counts as "watched"
            // for progress purposes.
            $table->unsignedTinyInteger('min_session_percent')->default(75);
 
            // Basic = 6, null = unlimited/derived from course duration (Diploma, Crash).
            $table->unsignedTinyInteger('max_weeks')->nullable();
 
            // course_only    = only a final course certificate
            // week_optional  = trainer can additionally issue per-week certs
            // both_required  = week certs required to unlock the course cert
            $table->enum('certificate_mode', ['course_only', 'week_optional', 'both_required'])
                ->default('course_only');
 
            // false for Crash Course — user's clock can't be paused/held.
            $table->boolean('allow_pause')->default(true);
 
            $table->timestamps();
 
            $table->unique('course_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_type_settings');
    }
};
