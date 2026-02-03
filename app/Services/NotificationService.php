<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Send notification to a specific user
     */
    public static function send($userId, $title, $message, $url = null, $icon = '🔔', $type = 'info')
    {
        return DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\SystemNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $userId,
            'data' => json_encode([
                'title' => $title,
                'message' => $message,
                'url' => $url ?? '/admin/dashboard',
                'icon' => $icon,
                'type' => $type
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Send notification to all admins
     */
    public static function notifyAdmins($title, $message, $url = null, $icon = '⚠️', $type = 'info')
    {
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        
        foreach ($admins as $admin) {
            self::send($admin->id, $title, $message, $url, $icon, $type);
        }
        
        return count($admins);
    }

    /**
     * New booking notification
     */
    public static function newBooking($booking)
    {
        // Notify photographer
        self::send(
            $booking->photographer_id,
            '📅 New Booking Request',
            "New booking from {$booking->client->name} for {$booking->package->name}",
            '/photographer/bookings/' . $booking->id,
            '📅',
            'success'
        );

        // Notify admins
        self::notifyAdmins(
            '📅 New Booking Created',
            "Booking #{$booking->id} created by {$booking->client->name}",
            '/admin/bookings',
            '📅',
            'info'
        );
    }

    /**
     * Booking status changed notification
     */
    public static function bookingStatusChanged($booking, $oldStatus, $newStatus)
    {
        // Notify client
        self::send(
            $booking->client_id,
            '📅 Booking Status Updated',
            "Your booking #{$booking->id} is now {$newStatus}",
            '/bookings/' . $booking->id,
            '📅',
            $newStatus === 'confirmed' ? 'success' : 'info'
        );

        // Notify admins
        self::notifyAdmins(
            '📅 Booking Status Changed',
            "Booking #{$booking->id} changed from {$oldStatus} to {$newStatus}",
            '/admin/bookings/' . $booking->id,
            '📅',
            'info'
        );
    }

    /**
     * New verification request notification
     */
    public static function newVerificationRequest($photographer)
    {
        self::notifyAdmins(
            '✅ New Verification Request',
            "{$photographer->user->name} submitted verification documents",
            '/admin/verifications',
            '✅',
            'warning'
        );
    }

    /**
     * Verification approved notification
     */
    public static function verificationApproved($photographer)
    {
        self::send(
            $photographer->user_id,
            '🎉 Verification Approved!',
            'Congratulations! Your photographer profile has been verified',
            '/photographer/profile',
            '🎉',
            'success'
        );
    }

    /**
     * Verification rejected notification
     */
    public static function verificationRejected($photographer, $reason)
    {
        self::send(
            $photographer->user_id,
            '❌ Verification Rejected',
            "Your verification was rejected. Reason: {$reason}",
            '/photographer/profile',
            '❌',
            'error'
        );
    }

    /**
     * New review notification
     */
    public static function newReview($review)
    {
        // Notify photographer
        self::send(
            $review->photographer->user_id,
            '⭐ New Review Received',
            "{$review->client->name} left you a {$review->rating}-star review",
            '/photographer/reviews',
            '⭐',
            'success'
        );

        // Notify admins
        self::notifyAdmins(
            '⭐ New Review Posted',
            "{$review->client->name} reviewed {$review->photographer->user->name}",
            '/admin/reviews',
            '⭐',
            'info'
        );
    }

    /**
     * Payment received notification
     */
    public static function paymentReceived($transaction)
    {
        // Notify photographer
        if ($transaction->photographer_id) {
            self::send(
                $transaction->photographer_id,
                '💰 Payment Received',
                "You received ৳{$transaction->amount} for booking #{$transaction->booking_id}",
                '/photographer/transactions',
                '💰',
                'success'
            );
        }

        // Notify admins
        self::notifyAdmins(
            '💰 New Payment',
            "Payment of ৳{$transaction->amount} received for booking #{$transaction->booking_id}",
            '/admin/transactions',
            '💰',
            'success'
        );
    }

    /**
     * New competition submission notification
     */
    public static function newCompetitionSubmission($submission)
    {
        self::notifyAdmins(
            '🏆 New Competition Entry',
            "{$submission->photographer->user->name} submitted to {$submission->competition->title}",
            '/admin/competitions/' . $submission->competition_id,
            '🏆',
            'info'
        );
    }

    /**
     * Competition deadline reminder notification
     */
    public static function competitionDeadlineReminder($competition)
    {
        // Get all participants
        $participants = $competition->submissions()->with('photographer.user')->get();
        
        foreach ($participants as $submission) {
            self::send(
                $submission->photographer->user_id,
                '⏰ Competition Deadline Approaching',
                "'{$competition->title}' ends in 24 hours!",
                '/competitions/' . $competition->id,
                '⏰',
                'warning'
            );
        }

        // Notify admins
        self::notifyAdmins(
            '⏰ Competition Ending Soon',
            "'{$competition->title}' ends in 24 hours",
            '/admin/competitions/' . $competition->id,
            '⏰',
            'warning'
        );
    }

    /**
     * New event registration notification
     */
    public static function newEventRegistration($registration)
    {
        self::notifyAdmins(
            '📸 New Event Registration',
            "{$registration->user->name} registered for {$registration->event->title}",
            '/admin/events/' . $registration->event_id,
            '📸',
            'info'
        );
    }

    /**
     * User suspended notification
     */
    public static function userSuspended($user, $reason)
    {
        self::send(
            $user->id,
            '🚫 Account Suspended',
            "Your account has been suspended. Reason: {$reason}",
            '/support',
            '🚫',
            'error'
        );
    }

    /**
     * Welcome notification for new users
     */
    public static function welcome($user)
    {
        self::send(
            $user->id,
            '🎉 Welcome to Photographer SB!',
            'Thank you for joining our platform. Complete your profile to get started.',
            $user->role === 'photographer' ? '/photographer/profile' : '/profile',
            '🎉',
            'success'
        );
    }
}
