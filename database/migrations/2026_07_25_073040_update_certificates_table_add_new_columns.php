<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {

            // Keep old enrollment_id if still needed

            $table->foreignId('user_id')
                ->nullable()
                ->after('certificate_number')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->after('user_id')
                ->constrained('course_categories')
                ->nullOnDelete();

            $table->foreignId('course_id')
                ->nullable()
                ->after('category_id')
                ->constrained('courses')
                ->nullOnDelete();

            $table->unsignedBigInteger('demo_submission_id')
                ->nullable()
                ->after('course_week_id');

            $table->enum('type', ['demo', 'week', 'level', 'course'])
                ->default('course')
                ->after('demo_submission_id');

            $table->enum('status', ['locked', 'pending_admin_approval', 'unlocked'])
                ->default('locked')
                ->after('type');

            $table->decimal('completion_percent', 5, 2)
                ->nullable()
                ->after('status');

            $table->string('grade')
                ->nullable()
                ->after('completion_percent');

            $table->foreignId('approved_by')
                ->nullable()
                ->after('grade')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable()
                ->after('approved_by');

            // issued_at already exists in the old table, so don't add it again.

            $table->timestamp('downloaded_at')
                ->nullable()
                ->change(); // Keep existing column but make sure it's nullable if needed
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {

            $table->dropForeign(['user_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['course_id']);
            $table->dropForeign(['approved_by']);

            $table->dropColumn([
                'user_id',
                'category_id',
                'course_id',
                'demo_submission_id',
                'type',
                'status',
                'completion_percent',
                'grade',
                'approved_by',
                'approved_at',
            ]);
        });
    }
};