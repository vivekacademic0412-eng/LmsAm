<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_setup_forms', function (Blueprint $table) {
            $table->id();

            // 01. School Basics
            $table->string('school_name')->nullable();
            $table->string('board_affiliation')->nullable();
            $table->string('address')->nullable();
            $table->string('grades_offered')->nullable();
            $table->string('student_strength')->nullable();

            // 02. Existing Lab, Room & Space
            $table->enum('existing_lab', ['dedicated_room', 'shared_partial', 'new_room'])->nullable();
            $table->string('room_size')->nullable();
            $table->string('seating_capacity')->nullable();
            $table->json('furniture')->nullable(); // ['work_tables','lockable_storage','display_board']

            // 03. Power, Network & Internet
            $table->string('power_points')->nullable();
            $table->string('backup_power')->nullable();
            $table->enum('internet_availability', ['wifi', 'lan', 'not_yet'])->nullable();
            $table->string('internet_speed')->nullable();
            $table->string('school_devices')->nullable();

            // 04. Students & Grades to Enroll
            $table->string('enroll_grades')->nullable();
            $table->string('expected_students')->nullable();
            $table->enum('session_frequency', ['weekly', 'twice_weekly', 'flexible'])->nullable();

            // 05. Timeline, Budget & Procurement
            $table->string('start_date')->nullable();
            $table->string('annual_budget')->nullable();
            $table->enum('procurement_process', ['principal', 'trust', 'tender'])->nullable();
            $table->text('lab_goals')->nullable();

            // 06. Point of Contact
            $table->string('contact_name')->nullable();
            $table->string('designation')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // Sign-off
            $table->string('signature')->nullable();
            $table->date('sig_date')->nullable();

            // Sync status with LMS
            $table->boolean('synced_to_lms')->default(false);
            $table->string('lms_reference_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_setup_forms');
    }
};
