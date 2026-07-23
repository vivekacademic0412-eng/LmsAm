<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_users', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('full_name');

            $table->string('email');

            $table->string('phone', 20);

            $table->foreignId('education_level_id')
                ->constrained('education_levels')
                ->cascadeOnDelete();

            $table->foreignId('interest_area_id')
                ->constrained('course_categories')
                ->cascadeOnDelete();

            $table->foreignId('preferred_course_id')
                ->constrained('courses')
                 ->nullable();


            $table->string('ip_address', 45)
                ->nullable();

            $table->foreignId('demo_feature_video_id')
                ->nullable()
                ->constrained('demo_feature_videos')
                ->nullOnDelete();

            $table->unsignedTinyInteger('progress_demo')
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_users');
    }
};