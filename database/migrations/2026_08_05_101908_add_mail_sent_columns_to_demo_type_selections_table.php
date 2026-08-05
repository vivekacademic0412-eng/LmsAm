<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_type_selections', function (Blueprint $table) {
            $table->unsignedInteger('mail_sent_count')->default(0)->after('is_confirm');
            $table->timestamp('mail_sent_at')->nullable()->after('mail_sent_count');
        });
    }

    public function down(): void
    {
        Schema::table('demo_type_selections', function (Blueprint $table) {
            $table->dropColumn([
                'mail_sent_count',
                'mail_sent_at',
            ]);
        });
    }
};