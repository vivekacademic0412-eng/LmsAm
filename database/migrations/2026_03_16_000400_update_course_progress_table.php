<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('course_progress') && Schema::hasColumn('course_progress', 'course_day_item_id')) {
            $hasEnrollmentFk = ! empty(DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'course_progress'
                  AND COLUMN_NAME = 'course_enrollment_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            "));
            $hasDayItemFk = ! empty(DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'course_progress'
                  AND COLUMN_NAME = 'course_day_item_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            "));
            $hasUniqueIndex = ! empty(DB::select("
                SHOW INDEX FROM course_progress WHERE Key_name = 'unique_enrollment_item_progress'
            "));

            Schema::table('course_progress', function (Blueprint $table) use ($hasEnrollmentFk, $hasDayItemFk, $hasUniqueIndex) {
                if ($hasEnrollmentFk) {
                    $table->dropForeign(['course_enrollment_id']);
                }
                if ($hasDayItemFk) {
                    $table->dropForeign(['course_day_item_id']);
                }
                if ($hasUniqueIndex) {
                    $table->dropUnique('unique_enrollment_item_progress');
                }
                $table->dropColumn('course_day_item_id');
            });
        }

        Schema::table('course_progress', function (Blueprint $table) {
            if (! Schema::hasColumn('course_progress', 'course_session_item_id')) {
                $table->foreignId('course_session_item_id')
                    ->after('course_enrollment_id')
                    ->constrained('course_session_items')
                    ->cascadeOnDelete();
                $table->unique(['course_enrollment_id', 'course_session_item_id'], 'unique_enrollment_item_progress');
            }
        });

        if (Schema::hasTable('course_progress')) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'course_progress'
                  AND COLUMN_NAME = 'course_enrollment_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            if (empty($foreignKeys)) {
                Schema::table('course_progress', function (Blueprint $table) {
                    $table->foreign('course_enrollment_id')
                        ->references('id')
                        ->on('course_enrollments')
                        ->cascadeOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('course_progress') && Schema::hasColumn('course_progress', 'course_session_item_id')) {
            $hasEnrollmentFk = ! empty(DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'course_progress'
                  AND COLUMN_NAME = 'course_enrollment_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            "));
            $hasSessionItemFk = ! empty(DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'course_progress'
                  AND COLUMN_NAME = 'course_session_item_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            "));
            $hasUniqueIndex = ! empty(DB::select("
                SHOW INDEX FROM course_progress WHERE Key_name = 'unique_enrollment_item_progress'
            "));

            Schema::table('course_progress', function (Blueprint $table) use ($hasEnrollmentFk, $hasSessionItemFk, $hasUniqueIndex) {
                if ($hasEnrollmentFk) {
                    $table->dropForeign(['course_enrollment_id']);
                }
                if ($hasSessionItemFk) {
                    $table->dropForeign(['course_session_item_id']);
                }
                if ($hasUniqueIndex) {
                    $table->dropUnique('unique_enrollment_item_progress');
                }
                $table->dropColumn('course_session_item_id');
            });
        }

        Schema::table('course_progress', function (Blueprint $table) {
            if (! Schema::hasColumn('course_progress', 'course_day_item_id')) {
                $table->foreignId('course_day_item_id')
                    ->after('course_enrollment_id')
                    ->constrained('course_day_items')
                    ->cascadeOnDelete();
                $table->unique(['course_enrollment_id', 'course_day_item_id'], 'unique_enrollment_item_progress');
            }
        });

        if (Schema::hasTable('course_progress')) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'course_progress'
                  AND COLUMN_NAME = 'course_enrollment_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            if (empty($foreignKeys)) {
                Schema::table('course_progress', function (Blueprint $table) {
                    $table->foreign('course_enrollment_id')
                        ->references('id')
                        ->on('course_enrollments')
                        ->cascadeOnDelete();
                });
            }
        }
    }
};
