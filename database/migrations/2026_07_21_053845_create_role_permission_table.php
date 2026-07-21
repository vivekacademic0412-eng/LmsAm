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
         Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role');        // superadmin | admin | manager_hr | it | trainer | student
             $table->foreignId('module_id')
                ->nullable()
                ->constrained('modules')
                ->nullOnDelete();
            $table->string('module_key');  // matches nav_items.module_key
            $table->boolean('can_view')->default(0);
            $table->boolean('can_create')->default(0);
            $table->boolean('can_edit')->default(0);
            $table->boolean('can_delete')->default(0);
            $table->timestamps();

            $table->unique(['role', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permission');
    }
};
