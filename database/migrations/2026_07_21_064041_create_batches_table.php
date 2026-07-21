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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
 
            $table->string('batch_code')->unique();
            $table->enum('mode', ['online', 'offline'])->default('online');
 
            $table->date('start_date');
            // start_date + course_type_settings.exception_days for this batch's course type.
            $table->date('zero_day_date')->nullable();
 
            $table->unsignedTinyInteger('max_weeks')->nullable();
 
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])
                ->default('upcoming');
 
            $table->foreignId('created_by')->constrained('users');
 
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
