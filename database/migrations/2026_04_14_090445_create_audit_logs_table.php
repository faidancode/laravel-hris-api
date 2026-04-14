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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('action'); // CREATE, UPDATE, DELETE
            $table->string('entity_type');
            $table->uuid('entity_id');

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Index
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
