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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_code')->unique(); // SB-CERT-2026-00001
            $table->foreignId('template_id')->constrained('certificate_templates')->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('cascade');
            $table->foreignId('competition_id')->nullable()->constrained('competitions')->onDelete('cascade');
            $table->foreignId('issued_to_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('issued_to_name'); // Name of participant (may not be a user)
            $table->string('issued_to_email')->nullable();
            $table->date('issue_date');
            $table->date('valid_until')->nullable(); // Optional expiry date
            $table->string('verification_qr_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['issued', 'revoked'])->default('issued');
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('event_id');
            $table->index('issued_to_user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
