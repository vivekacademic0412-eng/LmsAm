<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_tasks', function (Blueprint $table): void {
            $table->string('task_video_path')->nullable()->after('resource_file_size');
            $table->string('task_video_name')->nullable()->after('task_video_path');
            $table->string('task_video_mime')->nullable()->after('task_video_name');
            $table->unsignedBigInteger('task_video_size')->nullable()->after('task_video_mime');
        });
    }

    public function down(): void
    {
        Schema::table('demo_tasks', function (Blueprint $table): void {
            $table->dropColumn([
                'task_video_path',
                'task_video_name',
                'task_video_mime',
                'task_video_size',
            ]);
        });
    }
};
