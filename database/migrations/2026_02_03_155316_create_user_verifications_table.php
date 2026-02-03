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
        Schema::create('user_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('verification_type', ['phone', 'email', 'nid', 'business', 'studio']); 
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('document_url')->nullable();
            $table->string('document_number')->nullable();
            $table->string('document_type')->nullable(); // nid_front, nid_back, trade_license, etc
            $table->foreignId('verified_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('expires_at')->nullable(); // for renewable verifications
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'verification_type']);
            $table->index(['verification_status']);
            $table->unique(['user_id', 'verification_type']); // one verification per type per user
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_verifications');
    }
};
