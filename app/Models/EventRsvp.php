<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventRsvp extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_id',
        'user_id',
        'rsvp_status',
        'responded_at',
        'check_in_at',
        'ticket_purchased',
        'special_requirements',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'check_in_at' => 'datetime',
        'ticket_purchased' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
