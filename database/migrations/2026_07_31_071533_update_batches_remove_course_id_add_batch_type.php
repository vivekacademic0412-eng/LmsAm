<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {

            // Remove course_id
            if (Schema::hasColumn('batches', 'course_id')) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            }

            // Remove trainer_id
            if (Schema::hasColumn('batches', 'trainer_id')) {
                $table->dropForeign(['trainer_id']);
                $table->dropColumn('trainer_id');
            }

            // Add batch_type
            // if (!Schema::hasColumn('batches', 'batch_type')) {
            //     $table->enum('batch_type', [
            //         'morning',
            //         'evening',
            //         'weekend',
            //         'custom',
            //     ])->after('id');
            // }
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {

            $table->foreignId('course_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('trainer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // $table->dropColumn('batch_type');
        });
    }
};