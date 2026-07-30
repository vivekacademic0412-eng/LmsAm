<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {

            $table->foreignId('batch_id')
                ->nullable()
                ->after('assigned_by')
                ->constrained('batches')
                ->nullOnDelete();

            $table->foreignId('course_level_id')
                ->nullable()
                ->after('batch_id')
                ->constrained('course_levels')
                ->nullOnDelete();

            $table->string('order_reference')->nullable()->after('status');

            $table->decimal('amount_paid', 10, 2)
                ->default(0)
                ->after('order_reference');

            $table->timestamp('registered_at')
                ->nullable()
                ->after('amount_paid');

            $table->date('zero_day_start_at')
                ->nullable()
                ->after('registered_at');

            $table->unsignedTinyInteger('progress_percent')
                ->default(0)
                ->after('zero_day_start_at');

            $table->timestamp('certificate_unlocked_at')
                ->nullable()
                ->after('progress_percent');
        });
    }

    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {

            $table->dropForeign(['batch_id']);
            $table->dropForeign(['course_level_id']);

            $table->dropColumn([
                'batch_id',
                'course_level_id',
                'order_reference',
                'amount_paid',
                'registered_at',
                'zero_day_start_at',
                'progress_percent',
                'certificate_unlocked_at',
            ]);
        });
    }
};