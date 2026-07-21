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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('enrollment_id')->constrained('course_enrollments')->cascadeOnDelete();
 
            // Null = full-course certificate. Set = an optional per-week
            // certificate a trainer chose to issue.
            $table->foreignId('course_week_id')->nullable()->constrained()->nullOnDelete();
 
            $table->string('certificate_number')->unique();
            $table->string('file_path')->nullable();
 
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('downloaded_at')->nullable();
 
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
