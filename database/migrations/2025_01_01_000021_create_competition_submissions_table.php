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
        Schema::create('competition_submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('competition_id')->constrained('competitions')->onDelete('cascade');
            $table->foreignId('photographer_id')->constrained('photographers')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('image_url');
            $table->string('thumbnail_url');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->date('date_taken')->nullable();
            $table->string('camera_make')->nullable();
            $table->string('camera_model')->nullable();
            $table->json('camera_settings')->nullable();
            $table->string('hashtags')->nullable();
            $table->boolean('is_watermarked')->default(false);
            $table->enum('status', ['pending_review', 'approved', 'rejected', 'disqualified'])->default('pending_review');
            $table->integer('view_count')->default(0);
            $table->integer('vote_count')->default(0);
            $table->decimal('judge_score', 5, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->integer('ranking')->nullable();
            $table->boolean('is_winner')->default(false);
            $table->string('winner_position')->nullable(); // 1st, 2nd, 3rd, etc.
            $table->timestamps();

            $table->unique(['competition_id', 'photographer_id'], 'comp_submissions_unique');
            $table->index(['competition_id', 'status']);
            $table->index(['photographer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_submissions');
    }
};
