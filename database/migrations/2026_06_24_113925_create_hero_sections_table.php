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
       Schema::create('hero_sections', function (Blueprint $table) {
            $table->id();

            $table->string('logo_path')->nullable();

            // h1: "Learn the [highlight] [bold] [suffix]"
            $table->string('heading_prefix');          // "Learn the"
            $table->string('heading_highlight');        // "AI-era skills"
            $table->string('heading_bold');              // "employers"
            $table->string('heading_suffix');            // "actually hire for."

            $table->text('lede');

            $table->string('cta_primary_label');
            $table->string('cta_primary_url');
            $table->string('cta_secondary_label')->nullable();
            $table->string('cta_secondary_url')->nullable();

            $table->string('mascot_image')->nullable();

            $table->string('guide_tag')->nullable();     // "Your Personal Guide"
            $table->string('guide_name')->nullable();    // "Hi, I'm Academic Mantra"
            $table->text('guide_text')->nullable();

            $table->json('hand_images')->nullable();      // ["theme/images/am21.png", ...]

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_sections');
    }
};
