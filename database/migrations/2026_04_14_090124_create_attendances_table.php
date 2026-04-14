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
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');

            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();

            $table->string('status'); // present, late, absent, leave
            $table->integer('late_minutes')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // FK
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();

            // Index
            $table->index(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
