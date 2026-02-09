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
        if (!Schema::hasTable('admin_error_logs')) {
            Schema::create('admin_error_logs', function (Blueprint $table) {
                $table->id();
                
                // Severity & Environment
                $table->enum('severity', ['P0', 'P1', 'P2'])->default('P1')->index();
                $table->string('environment', 20); // local, production, staging
                
                // Request Context
                $table->text('url')->nullable();
                $table->string('route_name')->nullable();
                $table->string('method', 10)->nullable(); // GET, POST, etc.
                $table->integer('status_code')->nullable()->index();
                
                // User Context
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('ip', 45)->nullable();
                $table->text('user_agent')->nullable();
                
                // Error Details
                $table->text('message');
                $table->string('exception_class')->nullable();
                $table->text('file')->nullable();
                $table->integer('line')->nullable();
                $table->longText('trace')->nullable(); // Only for super_admin
                
                // Deduplication
                $table->string('signature_hash', 64)->index(); // MD5/SHA256 of error signature
                $table->integer('occurrences')->default(1);
                $table->timestamp('first_seen_at')->useCurrent();
                $table->timestamp('last_seen_at')->useCurrent();
                
                // Status Management
                $table->boolean('is_muted')->default(false)->index();
                $table->boolean('is_resolved')->default(false)->index();
                $table->unsignedBigInteger('resolved_by_user_id')->nullable();
                $table->timestamp('resolved_at')->nullable();
                
                $table->timestamps();
                
                // Foreign Keys
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('resolved_by_user_id')->references('id')->on('users')->onDelete('set null');
                
                // Composite Index for performance
                $table->index(['is_resolved', 'is_muted', 'last_seen_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_error_logs');
    }
};
