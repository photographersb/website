<?php

namespace App\Services;

use App\Models\PhotographerNotification;
use App\Models\Booking;
use App\Models\Review;
use App\Models\CompetitionSubmission;
use App\Models\Event;

class PhotographerNotificationService
{
    /**
     * Notify photographer about new booking
     */
    public static function notifyNewBooking(Booking $booking)
    {
        if (!$booking->photographer_id) {
            return;
        }

        PhotographerNotification::createNotification(
            $booking->photographer_id,
            PhotographerNotification::TYPE_BOOKING_RECEIVED,
            'New Booking Received',
            "You have received a new booking from {$booking->client->name}.",
            [
                'booking_id' => $booking->id,
                'client_name' => $booking->client->name,
                'date' => $booking->booking_date,
            ],
            "/dashboard?tab=bookings&booking_id={$booking->id}"
        );
    }

    /**
     * Notify photographer about booking status change
     */
    public static function notifyBookingStatusChange(Booking $booking, $oldStatus, $newStatus)
    {
        if (!$booking->photographer_id) {
            return;
        }

        $messages = [
            'confirmed' => 'Your booking has been confirmed by the client.',
            'cancelled' => 'A booking has been cancelled by the client.',
            'completed' => 'A booking has been marked as completed.',
        ];

        $title = match($newStatus) {
            'confirmed' => 'Booking Confirmed',
            'cancelled' => 'Booking Cancelled',
            'completed' => 'Booking Completed',
            default => 'Booking Status Updated',
        };

        $type = match($newStatus) {
            'confirmed' => PhotographerNotification::TYPE_BOOKING_CONFIRMED,
            'cancelled' => PhotographerNotification::TYPE_BOOKING_CANCELLED,
            default => PhotographerNotification::TYPE_BOOKING_RECEIVED,
        };

        PhotographerNotification::createNotification(
            $booking->photographer_id,
            $type,
            $title,
            $messages[$newStatus] ?? "Booking status changed to {$newStatus}.",
            [
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
            "/dashboard?tab=bookings&booking_id={$booking->id}"
        );
    }

    /**
     * Notify photographer about new review
     */
    public static function notifyNewReview(Review $review)
    {
        if (!$review->photographer_id) {
            return;
        }

        $stars = str_repeat('⭐', $review->rating);

        PhotographerNotification::createNotification(
            $review->photographer_id,
            PhotographerNotification::TYPE_REVIEW_POSTED,
            'New Review Posted',
            "You received a {$stars} ({$review->rating}/5) review from {$review->reviewer->name}.",
            [
                'review_id' => $review->id,
                'rating' => $review->rating,
                'reviewer_name' => $review->reviewer->name,
            ],
            "/dashboard?tab=reviews&review_id={$review->id}"
        );
    }

    /**
     * Notify photographer about competition result
     */
    public static function notifyCompetitionResult(CompetitionSubmission $submission, $position = null, $prize = null)
    {
        if (!$submission->photographer_id) {
            return;
        }

        $title = $position ? "You Won {$position} Place!" : "Competition Results Announced";
        $message = $position 
            ? "Congratulations! Your submission '{$submission->title}' won {$position} place in {$submission->competition->title}."
            : "Results for {$submission->competition->title} have been announced. Check your submission status.";

        if ($prize) {
            $message .= " Prize: {$prize}";
        }

        PhotographerNotification::createNotification(
            $submission->photographer_id,
            PhotographerNotification::TYPE_COMPETITION_RESULT,
            $title,
            $message,
            [
                'competition_id' => $submission->competition_id,
                'submission_id' => $submission->id,
                'position' => $position,
                'prize' => $prize,
            ],
            "/competitions/{$submission->competition->slug}"
        );
    }

    /**
     * Notify photographer when competition voting starts
     */
    public static function notifyCompetitionVotingStarted(CompetitionSubmission $submission)
    {
        if (!$submission->photographer_id) {
            return;
        }

        PhotographerNotification::createNotification(
            $submission->photographer_id,
            PhotographerNotification::TYPE_COMPETITION_VOTING_STARTED,
            'Voting Started',
            "Public voting has started for {$submission->competition->title}! Share your submission to get more votes.",
            [
                'competition_id' => $submission->competition_id,
                'submission_id' => $submission->id,
            ],
            "/competitions/{$submission->competition->slug}"
        );
    }

    /**
     * Notify photographer about upcoming event
     */
    public static function notifyEventReminder($photographerId, Event $event, $daysUntil)
    {
        PhotographerNotification::createNotification(
            $photographerId,
            PhotographerNotification::TYPE_EVENT_REMINDER,
            'Event Reminder',
            "Reminder: {$event->title} is in {$daysUntil} days ({$event->event_date->format('M d, Y')}).",
            [
                'event_id' => $event->id,
                'days_until' => $daysUntil,
            ],
            "/events/{$event->slug}"
        );
    }

    /**
     * Create custom notification
     */
    public static function sendCustomNotification($photographerId, $title, $message, $data = null, $actionUrl = null, $type = 'info')
    {
        PhotographerNotification::createNotification(
            $photographerId,
            $type,
            $title,
            $message,
            $data,
            $actionUrl
        );
    }
}
