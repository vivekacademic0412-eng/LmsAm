<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_setup_forms', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')
                ->constrained('users')->nullOnDelete();

            $table->foreignId('traffic_source_id')->nullable()->after('user_id')
                ->constrained('traffic_sources')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lab_setup_forms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('traffic_source_id');
        });
    }
};