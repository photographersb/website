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
        // Photographers table - frequently filtered/sorted
        Schema::table('photographers', function (Blueprint $table) {
            $table->index('average_rating', 'idx_photographers_rating');
            $table->index('is_featured', 'idx_photographers_featured');
            $table->index(['is_featured', 'average_rating'], 'idx_photographers_feat_rating');
            $table->index('created_at', 'idx_photographers_created');
        });

        // Reviews table - filtered by photographer and status
        Schema::table('reviews', function (Blueprint $table) {
            $table->index('photographer_id', 'idx_reviews_photographer');
            $table->index('status', 'idx_reviews_status');
            $table->index(['photographer_id', 'status'], 'idx_reviews_phot_status');
        });

        // Bookings table - filtered by client, photographer, status
        Schema::table('bookings', function (Blueprint $table) {
            $table->index('client_id', 'idx_bookings_client');
            $table->index('photographer_id', 'idx_bookings_photographer');
            $table->index('status', 'idx_bookings_status');
            $table->index('event_date', 'idx_bookings_event_date');
        });

        // Events table - filtered by status, city, date
        Schema::table('events', function (Blueprint $table) {
            $table->index('organizer_id', 'idx_events_organizer');
            $table->index('city_id', 'idx_events_city');
            $table->index('status', 'idx_events_status');
            $table->index('event_date', 'idx_events_date');
            $table->index(['status', 'event_date'], 'idx_events_status_date');
        });

        // Competitions table - filtered by status
        Schema::table('competitions', function (Blueprint $table) {
            $table->index('status', 'idx_competitions_status');
            $table->index('submission_deadline', 'idx_competitions_deadline');
        });

        // Competition Submissions - filtered by competition, photographer
        Schema::table('competition_submissions', function (Blueprint $table) {
            $table->index('competition_id', 'idx_submissions_competition');
            $table->index('photographer_id', 'idx_submissions_photographer');
            $table->index('status', 'idx_submissions_status');
            $table->index(['competition_id', 'status'], 'idx_submissions_comp_status');
        });

        // Event RSVPs - check user RSVP status
        Schema::table('event_rsvps', function (Blueprint $table) {
            $table->index('event_id', 'idx_rsvps_event');
            $table->index(['event_id', 'user_id'], 'idx_rsvps_event_user');
        });

        // Inquiries table - photographer and client lookups
        Schema::table('inquiries', function (Blueprint $table) {
            $table->index('photographer_id', 'idx_inquiries_photographer');
            $table->index('client_id', 'idx_inquiries_client');
            $table->index('status', 'idx_inquiries_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropIndex('idx_inquiries_status');
            $table->dropIndex('idx_inquiries_client');
            $table->dropIndex('idx_inquiries_photographer');
        });

        Schema::table('event_rsvps', function (Blueprint $table) {
            $table->dropIndex('idx_rsvps_event_user');
            $table->dropIndex('idx_rsvps_event');
        });

        Schema::table('competition_submissions', function (Blueprint $table) {
            $table->dropIndex('idx_submissions_comp_status');
            $table->dropIndex('idx_submissions_status');
            $table->dropIndex('idx_submissions_photographer');
            $table->dropIndex('idx_submissions_competition');
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->dropIndex('idx_competitions_deadline');
            $table->dropIndex('idx_competitions_status');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('idx_events_status_date');
            $table->dropIndex('idx_events_date');
            $table->dropIndex('idx_events_status');
            $table->dropIndex('idx_events_city');
            $table->dropIndex('idx_events_organizer');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_event_date');
            $table->dropIndex('idx_bookings_status');
            $table->dropIndex('idx_bookings_photographer');
            $table->dropIndex('idx_bookings_client');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_phot_status');
            $table->dropIndex('idx_reviews_status');
            $table->dropIndex('idx_reviews_photographer');
        });

        Schema::table('photographers', function (Blueprint $table) {
            $table->dropIndex('idx_photographers_created');
            $table->dropIndex('idx_photographers_feat_rating');
            $table->dropIndex('idx_photographers_featured');
            $table->dropIndex('idx_photographers_rating');
        });
    }
};
