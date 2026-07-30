<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batch_students', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')
                ->constrained('batches')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('enrollment_id')
                ->nullable()
                ->constrained('course_enrollments')
                ->nullOnDelete();

            $table->timestamp('joined_at')->nullable();

            $table->enum('status', [
                'active',
                'completed',
                'dropped',
                'inactive'
            ])->default('active');

            $table->timestamps();

            // Prevent duplicate student assignments in the same batch
            $table->unique(['batch_id', 'user_id']);

            // Indexes
            $table->index('batch_id');
            $table->index('user_id');
            $table->index('enrollment_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_students');
    }
};