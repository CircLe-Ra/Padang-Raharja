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
        Schema::create('aspiration_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aspiration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('responder_id')->constrained('users');
            $table->text('response');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspiration_responses');
    }
};
