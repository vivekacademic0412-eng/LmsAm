<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');

            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();

            // present | absent | leave | half_day
            $table->string('status', 20)->default('absent');

            // pending | approved | rejected
            $table->string('approval_status', 20)->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('review_notes')->nullable();

            // self | manager | admin | system  (who created/edited this row)
            $table->string('marked_by', 20)->default('self');

            $table->string('ip_address', 64)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamps();

            // One attendance row per student per day.
            $table->unique(['user_id', 'date']);
            $table->index(['date', 'status']);
            $table->index(['approval_status', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};