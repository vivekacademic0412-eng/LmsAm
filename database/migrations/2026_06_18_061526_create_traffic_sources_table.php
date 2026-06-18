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
        Schema::create('traffic_sources', function (Blueprint $table) {
            $table->id();
 
            // Visitor identity (no auth required at this stage — they
            // haven't signed up yet, this fires the moment they land)
            $table->string('user_ip', 45)->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();
 
            // Where they came from
            $table->string('source', 100)->nullable()->index();      // facebook, partner-site, google, linkedin, youtube, direct
            $table->string('referrer_url', 1000)->nullable();         // raw HTTP referrer header
 
            // UTM params — the actual marketing attribution data
            $table->string('utm_source', 150)->nullable();
            $table->string('utm_medium', 150)->nullable();
            $table->string('utm_campaign', 150)->nullable();
            $table->string('utm_term', 150)->nullable();
            $table->string('utm_content', 150)->nullable();
 
            // Landing context
            $table->string('landing_page', 500)->nullable();          // full URL they landed on
 
            // Device / browser fingerprint (lightweight, not full UA parsing)
            $table->string('device', 50)->nullable();                 // mobile, desktop, tablet
            $table->string('browser', 100)->nullable();
            $table->string('platform', 100)->nullable();              // OS
            $table->text('user_agent')->nullable();
 
            // Link to the demo_user once they actually register (Phase 5+)
            // nullable because traffic is tracked BEFORE registration
            $table->unsignedBigInteger('demo_user_id')->nullable()->index();
 
            $table->timestamps();
 
            $table->index(['source', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic_sources');
    }
};
