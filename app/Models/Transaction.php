<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'booking_id',
        'photographer_id',
        'user_id',
        'transaction_id',
        'transaction_type',
        'reference_id',
        'reference_table',
        'amount',
        'currency',
        'payment_method',
        'gateway_reference',
        'gateway_response',
        'status',
        'commission_amount',
        'platform_fee',
        'net_amount',
        'failure_reason',
        'payment_date',
        'completed_at',
        'refund_amount',
        'refund_status',
        'refund_reason',
        'refund_reference',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'completed_at' => 'datetime',
        'refund_amount' => 'decimal:2',
        'refunded_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
