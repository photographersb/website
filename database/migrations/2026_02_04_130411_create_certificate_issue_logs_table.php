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
        Schema::create('certificate_issue_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('competition_submissions')->onDelete('cascade');
            $table->enum('action', ['auto_issued', 'manual_issued', 'downloaded', 'emailed', 'revoked', 'reissued', 'verified']);
            $table->foreignId('performed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('meta')->nullable(); // Additional context
            $table->timestamps();

            $table->index('submission_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_issue_logs');
    }
};
