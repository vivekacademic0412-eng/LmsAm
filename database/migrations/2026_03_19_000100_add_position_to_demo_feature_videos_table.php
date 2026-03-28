<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_feature_videos', function (Blueprint $table): void {
            $table->unsignedInteger('position')->nullable()->index();
        });

        $ids = DB::table('demo_feature_videos')
            ->orderBy('id')
            ->pluck('id');

        foreach ($ids as $index => $id) {
            DB::table('demo_feature_videos')
                ->where('id', $id)
                ->update(['position' => $index + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('demo_feature_videos', function (Blueprint $table): void {
            $table->dropIndex(['position']);
            $table->dropColumn('position');
        });
    }
};
