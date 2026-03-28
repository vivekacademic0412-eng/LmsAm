<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_tasks', function (Blueprint $table): void {
            $table->string('resource_file_path')->nullable()->after('resource_url');
            $table->string('resource_file_name')->nullable()->after('resource_file_path');
            $table->string('resource_file_mime')->nullable()->after('resource_file_name');
            $table->unsignedBigInteger('resource_file_size')->nullable()->after('resource_file_mime');
        });
    }

    public function down(): void
    {
        Schema::table('demo_tasks', function (Blueprint $table): void {
            $table->dropColumn([
                'resource_file_path',
                'resource_file_name',
                'resource_file_mime',
                'resource_file_size',
            ]);
        });
    }
};
