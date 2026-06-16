<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('course_categories')->cascadeOnDelete();
            // $table->foreignId('course_type_id')
            //     ->constrained('course_types');
            $table->integer('course_type_id');
            $table->integer('course_level_id');
            // $table->foreignId('course_level_id')
            //     ->nullable()
            //     ->constrained('course_levels');

            $table->string('title', 160);
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_hours')->default(1);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['category_id', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
