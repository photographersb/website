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
        Schema::create('admin_error_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('severity', ['P0', 'P1', 'P2', 'P3', 'P4'])->default('P2')->index();
            $table->enum('environment', ['local', 'staging', 'production'])->default('production')->index();
            $table->string('url')->nullable()->index();
            $table->string('route_name')->nullable()->index();
            $table->string('method')->default('GET'); // GET, POST, PUT, DELETE, PATCH
            $table->integer('status_code')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->ipAddress('ip')->nullable();
            $table->string('message')->index();
            $table->string('exception_class')->nullable();
            $table->string('file')->nullable();
            $table->integer('line')->nullable();
            $table->longText('trace')->nullable(); // Serialized stack trace
            $table->boolean('is_resolved')->default(false)->index();
            $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->boolean('is_muted')->default(false)->index(); // Mute duplicate signatures
            $table->string('error_signature')->nullable()->index(); // Hash of error for grouping
            $table->integer('occurrence_count')->default(1); // Track duplicate occurrences
            $table->timestamp('last_occurrence_at')->nullable();
            $table->text('notes')->nullable(); // Admin notes
            $table->timestamps();
            
            // Composite indexes for common queries
            $table->index(['severity', 'is_resolved']);
            $table->index(['environment', 'created_at']);
            $table->index(['error_signature', 'is_muted']);
        });

        // Failed jobs table already exists, but we can extend it if needed
        // We'll link failed jobs to error logs via exception analysis
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_error_logs');
    }
};
