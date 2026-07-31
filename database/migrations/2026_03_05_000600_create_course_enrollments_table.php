<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('trainer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('batch_id')
                ->nullable();

            $table->foreignId('course_level_id')
                ->nullable();

            $table->string('status');

            $table->string('order_reference')->nullable();

            $table->decimal('amount_paid', 10, 2)->default(0);

            $table->timestamp('enrolled_at');

            $table->timestamp('registered_at')->nullable();

            $table->date('zero_day_start_at')->nullable();

            $table->unsignedTinyInteger('progress_percent')->default(0);

            $table->timestamp('certificate_unlocked_at')->nullable();

            $table->timestamps();

            $table->unique(['course_id', 'student_id']);

            // Foreign Keys
            $table->foreign('batch_id')
                ->references('id')
                ->on('batches')
                ->nullOnDelete();

            $table->foreign('course_level_id')
                ->references('id')
                ->on('course_levels')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};