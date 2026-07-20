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
        Schema::create('module_categories', function (Blueprint $table) {

            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('icon')->nullable();

            $table->integer('sort_order')->default(0);

            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_categories');
    }
    /**
     * Reverse the migrations.
     */
    
};
