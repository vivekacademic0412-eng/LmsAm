<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crash_course_links', function (Blueprint $table) {
            $table->id();

            // Original Parent Course
            $table->foreignId('source_course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            // Auto-generated Crash Course
            $table->foreignId('crash_course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            // Crash Level Mapping
            $table->unsignedTinyInteger('crash_level');

            $table->timestamps();

            // One parent should have only one crash course
            $table->unique('source_course_id');

            // One crash course should belong to only one parent
            $table->unique('crash_course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crash_course_links');
    }
};
