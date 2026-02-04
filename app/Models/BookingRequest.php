<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class BookingRequest extends Model
{
    use Notifiable;

    protected $fillable = [
        'booking_code',
        'client_user_id',
        'photographer_user_id',
        'category_id',
        'city_id',
        'venue_address',
        'event_date',
        'event_time',
        'duration_hours',
        'budget_min',
        'budget_max',
        'notes',
        'status',
        'accepted_at',
        'declined_at',
        'cancelled_at',
        'completed_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'photographer_user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(BookingMessage::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(BookingStatusLog::class);
    }

    // Scopes
    public function scopeForClient($query, $userId)
    {
        return $query->where('client_user_id', $userId);
    }

    public function scopeForPhotographer($query, $userId)
    {
        return $query->where('photographer_user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'accepted']);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeAccepted(): bool
    {
        return $this->isPending();
    }

    public function canBeDeclined(): bool
    {
        return $this->isPending();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'accepted']);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'accepted' => 'badge-success',
            'declined' => 'badge-danger',
            'cancelled' => 'badge-secondary',
            'completed' => 'badge-info',
            default => 'badge-secondary',
        };
    }

    public function getUnreadMessageCount(): int
    {
        return $this->messages()
            ->where('sender_user_id', '!=', auth()->id())
            ->where('is_read', false)
            ->count();
    }
}
