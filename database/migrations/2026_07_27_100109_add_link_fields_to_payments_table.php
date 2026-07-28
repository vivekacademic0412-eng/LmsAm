<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Public payment-link token — student pays via /pay/{token}, no login needed
            if (!Schema::hasColumn('payments', 'token')) {
                $table->string('token', 64)->unique()->nullable()->after('user_id');
            }

            // What this payment is for
            if (!Schema::hasColumn('payments', 'type')) {
                $table->enum('type', ['demo', 'course'])->nullable()->after('token');
            }
            if (!Schema::hasColumn('payments', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('course_categories')->nullOnDelete()->after('type');
            }
            if (!Schema::hasColumn('payments', 'course_id')) {
                $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete()->after('category_id');
            }

            // Which admin generated this link for the student
            if (!Schema::hasColumn('payments', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('course_id');
            }

            // Generated invoice PDF path
            if (!Schema::hasColumn('payments', 'receipt_pdf_path')) {
                $table->string('receipt_pdf_path')->nullable()->after('invoice_no');
            }

            // Optional link expiry
            if (!Schema::hasColumn('payments', 'link_expires_at')) {
                $table->timestamp('link_expires_at')->nullable()->after('paid_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'token', 'type', 'category_id', 'course_id',
                'created_by', 'receipt_pdf_path', 'link_expires_at',
            ]);
        });
    }
};