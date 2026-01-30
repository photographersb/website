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
        Schema::create('trust_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photographer_id')->unique()->constrained('photographers')->onDelete('cascade');
            $table->boolean('phone_verified')->default(false);
            $table->boolean('email_verified')->default(false);
            $table->boolean('id_verified')->default(false);
            $table->integer('review_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->decimal('booking_completion_rate', 5, 2)->default(0); // percentage
            $table->decimal('response_time_avg', 5, 2)->nullable(); // in hours
            $table->decimal('profile_completeness', 5, 2)->default(0); // percentage
            $table->decimal('overall_score', 5, 2)->default(0); // 0-100
            $table->enum('trust_badge', ['none', 'verified', 'trusted', 'elite'])->default('none');
            $table->timestamps();

            $table->index(['overall_score', 'trust_badge']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trust_scores');
    }
};
