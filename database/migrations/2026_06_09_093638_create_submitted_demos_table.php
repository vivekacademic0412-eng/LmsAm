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
    {  Schema::create('submitted_demos', function (Blueprint $table) {
            $table->id();

            // user who submitted demo
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
               $table->foreignId('demo_user_id')->constrained()->onDelete('cascade');

            // course context (from session)
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();

            $table->string('demo_topic');
            $table->text('demo_description');

            // video file path
            $table->string('demo_video');
             $table->string('completion_score')->nullable();
            

            // optional tracking fields
            $table->string('status')->default('pending'); // pending, approved, rejected

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submitted_demos');
    }
};
