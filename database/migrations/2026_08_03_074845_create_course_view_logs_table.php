<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_view_logs', function (Blueprint $table) {
            $table->id();

            // Unique "transaction id" burned into the on-screen watermark for this
            // specific view — if footage leaks, this maps straight back to the row.
            $table->uuid('tid')->unique();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_session_item_id')->nullable()->constrained()->nullOnDelete();

            // 'viewed' | 'video_completed'
            $table->string('event', 40);

            $table->string('ip_address', 64)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'course_id']);
            $table->index(['course_session_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_view_logs');
    }
};