<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_session_item_id')->constrained()->cascadeOnDelete();

            // Behavioral signals only — see model docblock for what this can/can't detect.
            $table->enum('event', [
                'play', 'pause', 'ended', 'seek',
                'tab_hidden', 'tab_visible',
                'devtools_suspected', 'right_click_blocked',
                'fullscreen_exit', 'window_blur', 'window_focus',
            ]);

            $table->json('meta')->nullable(); // e.g. { "position_seconds": 42, "duration_seconds": 600 }
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            $table->index(['course_session_item_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_access_logs');
    }
};