<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add composite indexes to optimize common query patterns on public pages
     */
    public function up(): void
    {
        // Photographers: Optimize filtering by city, verification, and featured status
        // Note: Some indexes already exist, only adding missing ones
        Schema::table('photographers', function (Blueprint $table) {
            // These indexes already exist, skipping:
            // - idx_photographers_featured (is_featured)
            // - idx_photographers_city_verified_featured (city_id, is_verified, is_featured)
            // - idx_photographers_verified_rating (is_verified, average_rating)
        });

        // Events: Add missing composite indexes for optimization
        Schema::table('events', function (Blueprint $table) {
            // Skip idx_events_city and idx_events_status (already exist)
            // Skip idx_events_city_type_featured (already exists)
            // Add new composite indexes for common filter combinations
            $table->index(['event_date', 'status'], 'idx_events_date_status');
            $table->index(['is_featured', 'event_date'], 'idx_events_featured_date');
        });

        // Competitions: Add missing composite indexes
        Schema::table('competitions', function (Blueprint $table) {
            // Skip idx_competitions_status and idx_competitions_is_featured (already exist)
            // Add new composite indexes for listing pages
            $table->index(['status', 'is_featured'], 'idx_competitions_status_featured');
            $table->index(['voting_start_at', 'voting_end_at', 'status'], 'idx_competitions_dates_status');
            $table->index(['is_featured', 'voting_start_at'], 'idx_competitions_featured_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            // No indexes to drop (all were pre-existing)
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('idx_events_date_status');
            $table->dropIndex('idx_events_featured_date');
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->dropIndex('idx_competitions_status_featured');
            $table->dropIndex('idx_competitions_dates_status');
            $table->dropIndex('idx_competitions_featured_start');
        });
    }
};
