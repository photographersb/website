<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedPhotographerUpgrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'featured_photographer_id',
        'from_package',
        'to_package',
        'prorated_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'transaction_id',
        'reference_number',
        'upgraded_at',
        'notes',
    ];

    protected $casts = [
        'prorated_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'upgraded_at' => 'datetime',
    ];

    /**
     * Relationship: belongs to FeaturedPhotographer
     */
    public function featuredPhotographer()
    {
        return $this->belongsTo(FeaturedPhotographer::class);
    }

    /**
     * Calculate prorated price for upgrade
     * 
     * @param string $currentPackage
     * @param string $newPackage
     * @param \DateTime $endDate
     * @return array
     */
    public static function calculateUpgradePrice($currentPackage, $newPackage, $endDate)
    {
        $prices = [
            'Starter' => 999,
            'Professional' => 2499,
            'Enterprise' => 5999,
        ];

        $currentPrice = $prices[$currentPackage] ?? 999;
        $newPrice = $prices[$newPackage] ?? 999;
        $daysRemaining = now()->diffInDays($endDate);
        $totalDaysInMonth = 30;

        // Calculate daily rates
        $currentDailyRate = $currentPrice / $totalDaysInMonth;
        $newDailyRate = $newPrice / $totalDaysInMonth;

        // Credit for remaining days at current price
        $credit = $currentDailyRate * $daysRemaining;

        // Cost for remaining days at new price
        $newCost = $newDailyRate * $daysRemaining;

        // Prorated amount = new cost - credit
        $proratedAmount = max(0, $newCost - $credit);

        // Apply discount if upgrading to higher tier
        $discount = 0;
        if ($newPrice > $currentPrice) {
            // 10% loyalty discount for upgrading
            $discount = $proratedAmount * 0.10;
        }

        $totalAmount = $proratedAmount - $discount;

        return [
            'current_price' => $currentPrice,
            'new_price' => $newPrice,
            'days_remaining' => $daysRemaining,
            'credit_amount' => round($credit, 2),
            'prorated_amount' => round($proratedAmount, 2),
            'discount_amount' => round($discount, 2),
            'total_amount' => round(max(0, $totalAmount), 2),
        ];
    }

    /**
     * Mark upgrade as completed
     */
    public function markAsCompleted($transactionId = null)
    {
        $this->update([
            'payment_status' => 'completed',
            'transaction_id' => $transactionId,
            'upgraded_at' => now(),
        ]);

        // Update featured photographer package
        if ($this->featuredPhotographer) {
            $this->featuredPhotographer->update(['package_tier' => $this->to_package]);
        }

        return $this;
    }

    /**
     * Mark upgrade as failed
     */
    public function markAsFailed($reason = null)
    {
        $this->update([
            'payment_status' => 'failed',
            'notes' => $reason,
        ]);

        return $this;
    }

    /**
     * Scope: Completed upgrades
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope: Pending upgrades
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope: By payment method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }
}
