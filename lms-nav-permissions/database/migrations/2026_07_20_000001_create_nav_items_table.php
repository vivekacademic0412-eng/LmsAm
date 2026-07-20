<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nav_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('module_key')->unique(); // e.g. 'users', 'courses', 'demos' — links to role_permissions.module_key
            $table->string('label');
            $table->string('icon')->nullable();      // e.g. 'ti ti-users'
            $table->string('route')->nullable();      // e.g. 'admin.users.index'
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('nav_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nav_items');
    }
};
