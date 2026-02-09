<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotographerTip extends Model
{
    use HasFactory;

    protected $fillable = [
        'photographer_id',
        'user_id',
        'amount',
        'currency',
        'payment_method',
        'transaction_id',
        'status',
        'message',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Relationship: belongs to photographer
     */
    public function photographer()
    {
        return $this->belongsTo(Photographer::class);
    }

    /**
     * Relationship: belongs to user (tipper)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: completed tips only
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: pending tips
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted($transactionId = null)
    {
        $this->update([
            'status' => 'completed',
            'transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark as failed
     */
    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
        return $this;
    }

    /**
     * Get total tips for photographer
     */
    public static function getTotalTips($photographerId)
    {
        return self::where('photographer_id', $photographerId)
            ->completed()
            ->sum('amount');
    }

    /**
     * Get recent tips for photographer
     */
    public static function getRecentTips($photographerId, $limit = 5)
    {
        return self::where('photographer_id', $photographerId)
            ->completed()
            ->orderByDesc('paid_at')
            ->limit($limit)
            ->get();
    }
}
