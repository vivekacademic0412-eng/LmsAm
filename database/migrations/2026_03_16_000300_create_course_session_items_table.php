<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_session_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_session_id')->constrained('course_sessions')->cascadeOnDelete();
            $table->string('item_type', 30);
            $table->string('title', 180);
            $table->string('resource_type', 20)->nullable();
            $table->text('content')->nullable();
            $table->string('resource_url', 500)->nullable();
            $table->string('cloudinary_public_id', 255)->nullable();
            $table->string('cloudinary_resource_type', 30)->nullable();
            $table->string('cloudinary_format', 30)->nullable();
            $table->string('cloudinary_delivery_type', 30)->nullable();
            $table->timestamps();

            $table->unique(['course_session_id', 'item_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_session_items');
    }
};
