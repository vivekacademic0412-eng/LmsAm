<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_week_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_week_id')->constrained()->cascadeOnDelete();

            // Admin can enable a certificate specifically for this week (add-on)
            $table->boolean('certificate_enabled')->default(false);

            // Optional override of course-level min_completion_percent for this week only
            $table->unsignedTinyInteger('min_completion_percent')->nullable();

            $table->boolean('is_visible')->default(true);

            $table->timestamps();
            $table->unique('course_week_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_week_settings');
    }
};