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
        Schema::table('course_session_items', function (Blueprint $table) {
            // Null = this row owns its own content (e.g. a Basic Course item).
            // Set  = this row is a linked/mirrored item; its content is read
            //        from the item it points to (e.g. the matching Crash
            //        Course item pointing back at the Basic Course original).
            $table->foreignId('linked_from_item_id')->nullable()->after('id')
                ->constrained('course_session_items')->nullOnDelete();
        });
    }
 
    public function down(): void
    {
        Schema::table('course_session_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('linked_from_item_id');
        });
    }
};
