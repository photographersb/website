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
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('verification_type', ['phone', 'email', 'nid', 'trade_license'])->index();
            $table->enum('verification_status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->string('phone_number')->nullable();
            $table->string('email_address')->nullable();
            $table->string('document_type')->nullable(); // NID, Passport, Trade License
            $table->string('document_url')->nullable();
            $table->string('document_number')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['user_id', 'verification_type']);
            $table->index(['verification_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
