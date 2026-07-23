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
        Schema::create('onboarding_section_settings', function (Blueprint $table) {
            $table->id();
            $table->string('role');                 // matches User::ROLE_* values
            $table->string('section_key');           // personal | academic | program
            $table->boolean('editable')->default(true); // can this role edit this section after onboarding is completed?
            $table->timestamps();
            $table->unique(['role', 'section_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
