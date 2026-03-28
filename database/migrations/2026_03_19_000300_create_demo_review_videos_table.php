<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_review_videos', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('position')->nullable()->unique();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('youtube_url', 500);
            $table->string('youtube_id', 32);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_review_videos');
    }
};
