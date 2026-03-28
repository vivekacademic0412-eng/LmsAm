<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('course_session_items')) {
            return;
        }

        Schema::table('course_session_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('course_session_items', 'is_live')) {
                $table->boolean('is_live')->default(false)->after('resource_url');
            }

            if (! Schema::hasColumn('course_session_items', 'live_at')) {
                $table->timestamp('live_at')->nullable()->after('is_live');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('course_session_items')) {
            return;
        }

        Schema::table('course_session_items', function (Blueprint $table): void {
            $columnsToDrop = [];

            if (Schema::hasColumn('course_session_items', 'is_live')) {
                $columnsToDrop[] = 'is_live';
            }

            if (Schema::hasColumn('course_session_items', 'live_at')) {
                $columnsToDrop[] = 'live_at';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
