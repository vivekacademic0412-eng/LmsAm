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
        Schema::table('demo_access_tokens', function (Blueprint $table) {
            $table->boolean('used')->default(false)->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('demo_access_tokens', function (Blueprint $table) {
            $table->dropColumn('used');
        });
    }
};
