<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotographerNotification extends Model
{
    protected $fillable = [
        'photographer_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Notification types
    const TYPE_BOOKING_RECEIVED = 'booking_received';
    const TYPE_BOOKING_CONFIRMED = 'booking_confirmed';
    const TYPE_BOOKING_CANCELLED = 'booking_cancelled';
    const TYPE_REVIEW_POSTED = 'review_posted';
    const TYPE_COMPETITION_RESULT = 'competition_result';
    const TYPE_COMPETITION_VOTING_STARTED = 'competition_voting_started';
    const TYPE_EVENT_REMINDER = 'event_reminder';
    const TYPE_NEW_MESSAGE = 'new_message';

    /**
     * Photographer relationship
     */
    public function photographer()
    {
        return $this->belongsTo(Photographer::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Scope: unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: recent notifications
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Create notification helper
     */
    public static function createNotification($photographerId, $type, $title, $message, $data = null, $actionUrl = null)
    {
        return self::create([
            'photographer_id' => $photographerId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
        ]);
    }
}
