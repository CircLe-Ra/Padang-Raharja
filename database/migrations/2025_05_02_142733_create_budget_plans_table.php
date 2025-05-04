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
        Schema::create('budget_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->constrained('fiscal_years')->cascadeOnDelete();
            $table->foreignId('account_code_id')->constrained('account_codes')->cascadeOnDelete();
            $table->foreignId('funding_source_id')->nullable()->constrained('funding_sources')->cascadeOnDelete();
            $table->string('volume', 50)->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('budget', 20, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_plans');
    }
};
