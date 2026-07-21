<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Add onboarding gate columns to existing users table ──
        Schema::table('users', function (Blueprint $table) {
            $table->enum('onboarding_status', ['pending', 'in_progress', 'completed'])
                ->default('pending')->after('email_verified_at');
            $table->unsignedTinyInteger('onboarding_step')->default(1)->after('onboarding_status');
        });

        // ── Versioned policy content ──
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('code');            // e.g. 'enrollment_terms'
            $table->string('version');         // e.g. 'v1.3'
            $table->string('title');
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['code', 'version']);
        });

        Schema::create('policy_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained()->cascadeOnDelete();
            $table->string('section_key');     // eligibility, fees, attendance, conduct, ip, exam, privacy, deliverables, liability
            $table->string('title');
            $table->longText('body');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ── Legal proof of consent ──
        Schema::create('policy_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('policy_id')->constrained();
            $table->string('policy_version');
            $table->ipAddress('ip_address');
            $table->string('user_agent', 512)->nullable();
            $table->boolean('declaration_confirmed')->default(false);
            $table->boolean('terms_agreed')->default(false);
            $table->boolean('marketing_opt_in')->default(false);
            $table->timestamp('accepted_at');
            $table->timestamps();
        });

        // ── Step 1: Personal details ──
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say']);
            $table->string('category')->nullable();
            $table->string('mobile_number');
            $table->string('whatsapp_number')->nullable();
            $table->string('email');
            $table->string('city_district');
            $table->text('residential_address');
            $table->enum('id_proof_type', ['aadhaar', 'passport', 'pan', 'voter_id', 'driving_licence']);
            $table->string('id_number');
            $table->timestamps();
        });

        // ── Step 2: Academic background ──
        Schema::create('academic_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('highest_qualification');
            $table->string('percentage_cgpa');
            $table->string('institution_name');
            $table->unsignedSmallInteger('year_of_passing');
            $table->enum('experience_level', ['fresher', '0-1', '1-2', '2+']);
            $table->string('guardian_name');
            $table->string('guardian_mobile');
            $table->timestamps();
        });

        // ── Step 3: Program selection ──
        Schema::create('program_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_category_id')->nullable();
            $table->foreignId('program_id')->nullable();
            $table->foreignId('batch_id')->nullable();
            $table->enum('mode_of_learning', ['online', 'offline', 'hybrid']);
            $table->date('preferred_start_date');
            $table->string('referral_source')->nullable(); // google, social media, friend/referral, college fair, counsellor, walk-in
            $table->text('career_goal')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->timestamps();
        });

        // ── Step 3: Documents & photos ──
        Schema::create('onboarding_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('doc_type', ['photo', 'id_proof', 'marksheet_certificate', 'experience_letter']);
            $table->string('file_path');
            $table->string('original_name');
            $table->unsignedBigInteger('file_size');
            $table->timestamp('uploaded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_documents');
        Schema::dropIfExists('program_enrollments');
        Schema::dropIfExists('academic_backgrounds');
        Schema::dropIfExists('student_profiles');
        Schema::dropIfExists('policy_acceptances');
        Schema::dropIfExists('policy_sections');
        Schema::dropIfExists('policies');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['onboarding_status', 'onboarding_step']);
        });
    }
};