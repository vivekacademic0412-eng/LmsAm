<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_tasks', function (Blueprint $table): void {
            $table->string('ai_video_url')->nullable()->after('resource_url');
        });
    }

    public function down(): void
    {
        Schema::table('demo_tasks', function (Blueprint $table): void {
            $table->dropColumn('ai_video_url');
        });
    }
};
