<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_code')->unique();
            $table->foreignId('template_id')->nullable()->constrained('certificate_templates')->onDelete('set null');
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null');
            $table->foreignId('competition_id')->nullable()->constrained('competitions')->onDelete('set null');
            $table->foreignId('issued_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('issued_to_name')->nullable();
            $table->string('issued_to_email')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('verification_qr_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('status')->default('issued');
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['competition_id', 'status']);
            $table->index(['event_id', 'status']);
            $table->index('issued_to_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
