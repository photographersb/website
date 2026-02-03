<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPreferenceController extends Controller
{
    use ApiResponse;
    /**
     * Get user's notification preferences
     */
    public function index()
    {
        $user = Auth::user();

        $defaultPreferences = [
            'booking_created' => ['mail' => true, 'sms' => false, 'database' => true],
            'booking_status_updated' => ['mail' => true, 'sms' => false, 'database' => true],
            'quote_received' => ['mail' => true, 'sms' => false, 'database' => true],
            'payment_received' => ['mail' => true, 'sms' => false, 'database' => true],
            'competition_deadline_reminder' => ['mail' => true, 'sms' => false, 'database' => true],
            'event_reminder' => ['mail' => true, 'sms' => false, 'database' => true],
            'review_request' => ['mail' => true, 'sms' => false, 'database' => true],
            'marketing_emails' => false,
            'weekly_digest' => true,
        ];

        $preferences = $user->preferences ?? $defaultPreferences;

        return $this->success([
            'user_id' => $user->id,
            'preferences' => $preferences,
        ], 'Notification preferences retrieved successfully');
    }

    /**
     * Update notification preferences
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'preferences' => 'required|array',
            'preferences.booking_created' => 'array',
            'preferences.booking_status_updated' => 'array',
            'preferences.quote_received' => 'array',
            'preferences.payment_received' => 'array',
            'preferences.competition_deadline_reminder' => 'array',
            'preferences.event_reminder' => 'array',
            'preferences.review_request' => 'array',
            'preferences.marketing_emails' => 'boolean',
            'preferences.weekly_digest' => 'boolean',
        ]);

        $user->update(['preferences' => $validated['preferences']]);

        return $this->success([
            'preferences' => $user->preferences,
        ], 'Notification preferences updated');
    }

    /**
     * Reset preferences to defaults
     */
    public function reset()
    {
        $user = Auth::user();

        $defaultPreferences = [
            'booking_created' => ['mail' => true, 'sms' => false, 'database' => true],
            'booking_status_updated' => ['mail' => true, 'sms' => false, 'database' => true],
            'quote_received' => ['mail' => true, 'sms' => false, 'database' => true],
            'payment_received' => ['mail' => true, 'sms' => false, 'database' => true],
            'competition_deadline_reminder' => ['mail' => true, 'sms' => false, 'database' => true],
            'event_reminder' => ['mail' => true, 'sms' => false, 'database' => true],
            'review_request' => ['mail' => true, 'sms' => false, 'database' => true],
            'marketing_emails' => false,
            'weekly_digest' => true,
        ];

        $user->update(['preferences' => $defaultPreferences]);

        return $this->success([
            'preferences' => $user->preferences,
        ], 'Notification preferences reset to defaults');
    }

    /**
     * Get available notification channels
     */
    public function channels()
    {
        return $this->success([
            'channels' => [
                'mail' => 'Email notifications',
                'sms' => 'SMS notifications',
                'database' => 'In-app notifications',
            ],
            'notifications' => [
                'booking_created' => 'New Booking Created',
                'booking_status_updated' => 'Booking Status Updated',
                'quote_received' => 'Quote Received',
                'payment_received' => 'Payment Received',
                'competition_deadline_reminder' => 'Competition Deadline Reminder',
                'event_reminder' => 'Event Reminder',
                'review_request' => 'Review Request',
                'marketing_emails' => 'Marketing Emails',
                'weekly_digest' => 'Weekly Digest',
            ],
        ], 'Notification channels retrieved successfully');
    }
}
