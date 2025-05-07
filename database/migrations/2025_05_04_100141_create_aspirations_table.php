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
        Schema::create('aspirations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ticket')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('contact', 50)->nullable();
            $table->string('category', 50);
            $table->string('title', 200);
            $table->string('location')->nullable();
            $table->text('description');
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_public')->default(false);
            $table->string('status', 50)->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspirations');
    }
};
