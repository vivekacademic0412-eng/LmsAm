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
        Schema::create('session_progress', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('enrollment_id')->constrained('course_enrollments')->cascadeOnDelete();
            $table->foreignId('course_session_item_id')->constrained()->cascadeOnDelete();
 
            $table->unsignedInteger('watched_seconds')->default(0);
            $table->unsignedTinyInteger('percent_watched')->default(0);
 
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
 
            $table->timestamps();
 
            $table->unique(['enrollment_id', 'course_session_item_id']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('session_progress');
    }
};
