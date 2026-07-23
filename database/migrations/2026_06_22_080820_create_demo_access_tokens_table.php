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
      Schema::create('demo_access_tokens', function (Blueprint $table) {

    $table->id();

    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->string('token')->unique();

    $table->string('session_id')->nullable();

    $table->string('browser_fingerprint')->nullable();

    $table->dateTime('used_at')->nullable();
 
    $table->dateTime('expires_at');

    $table->boolean('is_completed')->default(false);

    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demo_access_tokens');
    }
};
