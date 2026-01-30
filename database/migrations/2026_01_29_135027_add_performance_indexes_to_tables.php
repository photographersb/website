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
            $table->index('city_id');
            $table->index('average_rating');
            $table->index('is_featured');
            $table->index(['is_featured', 'average_rating']); // Composite for default sort
            $table->index('created_at');
        });

        // Reviews table - filtered by photographer and status
        Schema::table('reviews', function (Blueprint $table) {
            $table->index('photographer_id');
            $table->index('status');
            $table->index(['photographer_id', 'status']); // Composite for published reviews
            $table->index('published_at');
        });

        // Bookings table - filtered by client, photographer, status
        Schema::table('bookings', function (Blueprint $table) {
            $table->index('client_id');
            $table->index('photographer_id');
            $table->index('status');
            $table->index('event_date');
            $table->index(['client_id', 'status']); // For client bookings list
        });

        // Events table - filtered by status, city, date
        Schema::table('events', function (Blueprint $table) {
            $table->index('organizer_id');
            $table->index('city_id');
            $table->index('status');
            $table->index('event_date');
            $table->index('is_featured');
            $table->index(['status', 'event_date']); // For published upcoming events
        });

        // Competitions table - filtered by status, category
        Schema::table('competitions', function (Blueprint $table) {
            $table->index('admin_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('submission_deadline');
        });

        // Competition Submissions - filtered by competition, photographer
        Schema::table('competition_submissions', function (Blueprint $table) {
            $table->index('competition_id');
            $table->index('photographer_id');
            $table->index('status');
            $table->index(['competition_id', 'status']); // For approved submissions
        });

        // Event RSVPs - check user RSVP status
        Schema::table('event_rsvps', function (Blueprint $table) {
            $table->index('event_id');
            $table->index(['event_id', 'user_id']); // Check if user RSVP'd
            $table->index('rsvp_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_rsvps', function (Blueprint $table) {
            $table->dropIndex(['rsvp_status']);
            $table->dropIndex(['event_id', 'user_id']);
            $table->dropIndex(['event_id']);
        });

        Schema::table('competition_submissions', function (Blueprint $table) {
            $table->dropIndex(['competition_id', 'status']);
            $table->dropIndex(['status']);
            $table->dropIndex(['photographer_id']);
            $table->dropIndex(['competition_id']);
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->dropIndex(['submission_deadline']);
            $table->dropIndex(['status']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['admin_id']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['status', 'event_date']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['event_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['organizer_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['client_id', 'status']);
            $table->dropIndex(['event_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['photographer_id']);
            $table->dropIndex(['client_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['published_at']);
            $table->dropIndex(['photographer_id', 'status']);
            $table->dropIndex(['status']);
            $table->dropIndex(['photographer_id']);
        });

        Schema::table('photographers', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_featured', 'average_rating']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['average_rating']);
            $table->dropIndex(['city_id']);
        });
    }
};
