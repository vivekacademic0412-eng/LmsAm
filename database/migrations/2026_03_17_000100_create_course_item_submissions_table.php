<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('course_item_submissions')) {
            return;
        }

        Schema::create('course_item_submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_enrollment_id')->constrained('course_enrollments')->cascadeOnDelete();
            $table->foreignId('course_session_item_id')->constrained('course_session_items')->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->string('submission_type', 20);
            $table->text('answer_text')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_mime', 120)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['course_enrollment_id', 'course_session_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_item_submissions');
    }
};
