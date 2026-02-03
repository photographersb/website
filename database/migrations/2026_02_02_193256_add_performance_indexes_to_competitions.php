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
        // Add performance indexes to competitions table
        Schema::table('competitions', function (Blueprint $table) {
            $table->index('is_featured', 'idx_competitions_is_featured');
            $table->index(['status', 'is_featured', 'submission_deadline'], 'idx_competitions_listing');
        });

        // Add performance indexes to competition_submissions table
        Schema::table('competition_submissions', function (Blueprint $table) {
            $table->index('vote_count', 'idx_submissions_vote_count');
            $table->index('final_score', 'idx_submissions_final_score');
            $table->index(['competition_id', 'status', 'vote_count'], 'idx_submissions_ranking');
        });

        // Add index to competition_votes for fraud detection
        Schema::table('competition_votes', function (Blueprint $table) {
            $table->index(['ip_address', 'created_at'], 'idx_votes_fraud_detection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropIndex('idx_competitions_is_featured');
            $table->dropIndex('idx_competitions_listing');
        });

        Schema::table('competition_submissions', function (Blueprint $table) {
            $table->dropIndex('idx_submissions_vote_count');
            $table->dropIndex('idx_submissions_final_score');
            $table->dropIndex('idx_submissions_ranking');
        });

        Schema::table('competition_votes', function (Blueprint $table) {
            $table->dropIndex('idx_votes_fraud_detection');
        });
    }
};
