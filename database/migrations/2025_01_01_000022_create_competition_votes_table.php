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
        Schema::create('competition_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('competition_submissions')->onDelete('cascade');
            $table->foreignId('voter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('competition_id')->constrained('competitions')->onDelete('cascade');
            $table->integer('vote_value')->default(1);
            $table->timestamp('voted_at');
            $table->string('ip_address')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_valid')->default(true); // For fraud detection
            $table->timestamps();

            $table->unique(['submission_id', 'voter_id']);
            $table->index(['competition_id', 'voted_at']);
            $table->index(['voter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_votes');
    }
};
