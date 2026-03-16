<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_week_id')->constrained('course_weeks')->cascadeOnDelete();
            $table->unsignedInteger('session_number');
            $table->string('title', 180);
            $table->timestamps();

            $table->unique(['course_week_id', 'session_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_sessions');
    }
};
