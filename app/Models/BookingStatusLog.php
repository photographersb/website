<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingStatusLog extends Model
{
    public $timestamps = false; // only created_at, no updated_at

    protected $fillable = [
        'booking_id',
        'old_status',
        'new_status',
        'changed_by_user_id',
        'reason',
        'notes',
        'ip_address',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
