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
        Schema::create('demo_type_selections', function (Blueprint $table) {
            $table->id();
 
            $table->unsignedBigInteger('traffic_source_id')->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('user_ip', 45)->nullable();
 
            // 'free' or 'paid'
            $table->enum('demo_type', ['free', 'paid'])->index();
 
            // For paid demos, the price shown at selection time (snapshot —
            // protects historical reporting even if pricing changes later)
            $table->decimal('amount', 10, 2)->nullable();
 
            // Links forward once the user proceeds (nullable until then)
            $table->unsignedBigInteger('demo_user_id')->nullable()->index();
            $table->unsignedBigInteger('payment_id')->nullable()->index();
 
            $table->timestamps();
 
            $table->foreign('traffic_source_id')
                ->references('id')->on('traffic_sources')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demo_type_selections');
    }
};
