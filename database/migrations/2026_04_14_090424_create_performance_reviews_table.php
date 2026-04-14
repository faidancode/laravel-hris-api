<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('reviewer_id')->constrained('users')->cascadeOnDelete();

            $table->string('period'); // e.g. 2026-Q1
            $table->integer('score');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};
