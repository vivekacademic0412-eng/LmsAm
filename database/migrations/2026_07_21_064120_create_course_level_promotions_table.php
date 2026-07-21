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
        Schema::create('course_level_promotions', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_category_id')->constrained('course_categories')->cascadeOnDelete();
 
            $table->foreignId('from_enrollment_id')->nullable()
                ->constrained('course_enrollments')->nullOnDelete();
 
            $table->foreignId('to_course_level_id')->constrained('course_levels')->cascadeOnDelete();
 
            $table->foreignId('promoted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('promoted_at')->useCurrent();
 
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('course_level_promotions');
    }
};
