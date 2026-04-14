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
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('employee_code')->unique();

            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('hire_date');

            $table->string('employment_status'); // active, resigned, terminated

            $table->foreignUuid('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('position_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignUuid('manager_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('department_id');
            $table->index('position_id');
            $table->index('manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
