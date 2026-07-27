<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_session_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_session_id')->constrained()->cascadeOnDelete();

            // If true, this session MUST be completed for the certificate to unlock
            $table->boolean('is_required_for_certificate')->default(false);

            // Live class link + timing (admin sets, student sees "Join Now" near this time)
            $table->string('meet_link')->nullable();
            $table->dateTime('meet_datetime')->nullable();

            $table->boolean('is_visible')->default(true);

            $table->timestamps();
            $table->unique('course_session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_session_settings');
    }
};