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
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            // Student Details
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20);

            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();

            $table->string('country')->default('India');

            // Course Details
            // $table->unsignedBigInteger('course_id')->nullable();
            // $table->string('course_name')->nullable();

            // Payment Details
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->nullable();

            $table->string('gateway')->nullable(); // Razorpay
            $table->string('order_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('invoice_no')->nullable();

            $table->enum('status', [
                'pending',
                'success',
                'failed',
                'refunded'
            ])->default('pending');

            // Additional
            $table->string('coupon_code')->nullable();
            $table->string('source')->nullable(); // Website, Google Ads

            $table->text('notes')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
