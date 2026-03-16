<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_day_items', function (Blueprint $table) {
            $table->string('cloudinary_public_id', 255)->nullable()->after('resource_url');
            $table->string('cloudinary_resource_type', 30)->nullable()->after('cloudinary_public_id');
            $table->string('cloudinary_format', 30)->nullable()->after('cloudinary_resource_type');
            $table->string('cloudinary_delivery_type', 30)->nullable()->after('cloudinary_format');
        });
    }

    public function down(): void
    {
        Schema::table('course_day_items', function (Blueprint $table) {
            $table->dropColumn([
                'cloudinary_public_id',
                'cloudinary_resource_type',
                'cloudinary_format',
                'cloudinary_delivery_type',
            ]);
        });
    }
};
