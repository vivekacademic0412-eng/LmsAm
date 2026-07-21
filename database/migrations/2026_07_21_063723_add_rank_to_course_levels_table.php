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
        Schema::table('course_levels', function (Blueprint $table) {
            // 1 = Beginner, 2 = Beginner+, 3 = Intermediate, 4 = Advanced.
            // Lets the promotion logic ask "is target rank == current rank + 1"
            // instead of hardcoding level names.
            $table->unsignedTinyInteger('rank')->default(0)->after('name');
        });
    }
 
    public function down(): void
    {
        Schema::table('course_levels', function (Blueprint $table) {
            $table->dropColumn('rank');
        });
    }
};
