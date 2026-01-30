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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('photographer_id')->constrained('photographers')->onDelete('cascade');
            $table->integer('rating')->default(5); // 1-5 stars
            $table->decimal('professionalism_score', 3, 2)->nullable();
            $table->decimal('quality_score', 3, 2)->nullable();
            $table->decimal('communication_score', 3, 2)->nullable();
            $table->decimal('value_score', 3, 2)->nullable();
            $table->decimal('delivery_score', 3, 2)->nullable();
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_verified_purchase')->default(true);
            $table->json('photo_urls')->nullable();
            $table->integer('helpful_count')->default(0);
            $table->integer('unhelpful_count')->default(0);
            $table->enum('status', ['pending', 'published', 'flagged', 'hidden'])->default('pending');
            $table->string('flag_reason')->nullable();
            $table->text('moderation_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['photographer_id', 'status', 'rating']);
            $table->index(['reviewer_id']);
            $table->index(['booking_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
