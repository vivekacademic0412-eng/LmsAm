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
        Schema::table('demo_feature_videos', function (Blueprint $table) {
            $table->foreignId('course_id')
                ->nullable()
                ->after('id')
                ->constrained('courses')
                ->nullOnDelete();

            $table->boolean('status')
                ->default(1)
                ->after('course_id');
        });
    }

    public function down(): void
    {
        Schema::table('demo_feature_videos', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn(['course_id', 'status']);
        });
    }
};
