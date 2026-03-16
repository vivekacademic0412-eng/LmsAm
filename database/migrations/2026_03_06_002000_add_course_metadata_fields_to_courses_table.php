<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('subcategory_id')
                ->nullable()
                ->after('category_id')
                ->constrained('course_categories')
                ->nullOnDelete();
            $table->string('short_description', 255)->nullable()->after('title');
            $table->string('language', 80)->nullable()->after('description');
            $table->string('thumbnail', 500)->nullable()->after('language');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subcategory_id');
            $table->dropColumn(['short_description', 'language', 'thumbnail']);
        });
    }
};
