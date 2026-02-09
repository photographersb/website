<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedPhotographerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'featured_photographer_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'reference_number',
        'payment_details',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * Relationship: belongs to FeaturedPhotographer
     */
    public function featuredPhotographer()
    {
        return $this->belongsTo(FeaturedPhotographer::class);
    }

    /**
     * Relationship: through FeaturedPhotographer to Photographer
     */
    public function photographer()
    {
        return $this->hasOneThrough(Photographer::class, FeaturedPhotographer::class, 'id', 'id', 'featured_photographer_id', 'photographer_id');
    }

    /**
     * Mark payment as completed
     */
    public function markAsCompleted($transactionId = null, $paymentDetails = null)
    {
        $this->update([
            'status' => 'completed',
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'payment_details' => $paymentDetails ?? $this->payment_details,
            'paid_at' => now(),
        ]);

        // Activate the featured photographer if payment is successful
        if ($this->featuredPhotographer) {
            $this->featuredPhotographer->update(['active' => true]);
        }

        return $this;
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'notes' => $reason,
        ]);

        return $this;
    }

    /**
     * Scope: Completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: By payment method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope: Recent payments (last 30 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    /**
     * Calculate total revenue from completed payments
     */
    public static function totalRevenue($days = null)
    {
        $query = self::completed();

        if ($days) {
            $query->where('created_at', '>=', now()->subDays($days));
        }

        return $query->sum('amount');
    }
}
