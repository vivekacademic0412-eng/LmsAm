<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            // ASSUMPTION: seats live directly on the batch. Drop this migration if
            // you already track capacity somewhere else and just tell me the column.
            if (! Schema::hasColumn('batches', 'max_seats')) {
                $table->unsignedInteger('max_seats')->nullable()->after('mode');
            }
            if (! Schema::hasColumn('batches', 'status')) {
                $table->string('status')->default('upcoming')->after('max_seats');
                // expected values: upcoming | active | completed | cancelled
            }
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['max_seats', 'status']);
        });
    }
};