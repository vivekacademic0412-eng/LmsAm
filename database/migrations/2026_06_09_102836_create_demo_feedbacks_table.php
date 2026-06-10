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
        Schema::create('demo_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
              $table->foreignId('demo_user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
 
            // Emoji reaction (overall experience)
            $table->string('emoji_reaction');           // 😍 😊 😐 😞 😡
            $table->string('emoji_label');              // "Loved it!" etc
 
            // Rating 1-5 (star rating)
            $table->tinyInteger('rating')->nullable();  // 1-5
 
            // Which aspect ratings (optional per-category)
            $table->tinyInteger('content_rating')->nullable();   // 1-5
            $table->tinyInteger('clarity_rating')->nullable();   // 1-5
            $table->tinyInteger('support_rating')->nullable();   // 1-5
 
            // Text feedback
            $table->text('message')->nullable();
 
            // What they liked / what to improve (tags)
            $table->json('liked_tags')->nullable();     // ["Easy to follow","Good examples",...]
            $table->json('improve_tags')->nullable();   // ["More examples","Longer video",...]
 
            // Would recommend?
            $table->boolean('would_recommend')->nullable();
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demo_feedbacks');
    }
};
