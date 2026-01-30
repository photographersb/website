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
        Schema::create('competition_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->onDelete('cascade');
            $table->foreignId('submission_id')->constrained('competition_submissions')->onDelete('cascade');
            $table->foreignId('judge_id')->constrained('users')->onDelete('cascade');
            
            // Scoring criteria (out of 10 each)
            $table->decimal('composition_score', 3, 1)->nullable();
            $table->decimal('technical_score', 3, 1)->nullable();
            $table->decimal('creativity_score', 3, 1)->nullable();
            $table->decimal('story_score', 3, 1)->nullable();
            $table->decimal('impact_score', 3, 1)->nullable();
            
            // Calculated total
            $table->decimal('total_score', 4, 1)->nullable();
            
            // Feedback
            $table->text('feedback')->nullable();
            $table->text('strengths')->nullable();
            $table->text('improvements')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamp('scored_at')->nullable();
            
            $table->timestamps();
            
            // Prevent duplicate scores from same judge
            $table->unique(['submission_id', 'judge_id']);
            
            // Indexes
            $table->index(['competition_id', 'judge_id']);
            $table->index(['submission_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_scores');
    }
};
