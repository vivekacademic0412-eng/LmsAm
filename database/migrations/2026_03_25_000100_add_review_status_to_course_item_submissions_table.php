<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('course_item_submissions') || Schema::hasColumn('course_item_submissions', 'review_status')) {
            return;
        }

        Schema::table('course_item_submissions', function (Blueprint $table): void {
            $table->string('review_status')
                ->default('pending_review')
                ->after('reviewed_at');

            $table->index('review_status');
        });

        DB::table('course_item_submissions')
            ->whereNotNull('reviewed_at')
            ->update(['review_status' => 'reviewed']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('course_item_submissions') || ! Schema::hasColumn('course_item_submissions', 'review_status')) {
            return;
        }

        Schema::table('course_item_submissions', function (Blueprint $table): void {
            $table->dropIndex(['review_status']);
            $table->dropColumn('review_status');
        });
    }
};
