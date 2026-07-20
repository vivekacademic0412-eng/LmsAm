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
        Schema::create('modules', function (Blueprint $table) {

            $table->id();

            $table->foreignId('category_id')
                ->constrained('module_categories')
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('modules')
                ->cascadeOnDelete();

            $table->string('module_key')->unique();

            $table->string('label');

            $table->string('icon')->nullable();

            $table->string('route')->nullable();

            $table->integer('sort_order')->default(0);

            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
