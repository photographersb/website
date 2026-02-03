<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotographerAchievement extends Model
{
    protected $fillable = [
        'photographer_id',
        'achievement_id',
        'progress',
        'is_unlocked',
        'unlocked_at',
    ];

    protected $casts = [
        'is_unlocked' => 'boolean',
        'unlocked_at' => 'datetime',
    ];

    public function photographer()
    {
        return $this->belongsTo(Photographer::class);
    }

    public function achievement()
    {
        return $this->belongsTo(Achievement::class);
    }

    /**
     * Check if achievement should be unlocked
     */
    public function checkAndUnlock()
    {
        if (!$this->is_unlocked && $this->progress >= $this->achievement->required_count) {
            $this->update([
                'is_unlocked' => true,
                'unlocked_at' => now(),
            ]);

            // Award points to photographer
            $stats = PhotographerStats::firstOrCreate(
                ['photographer_id' => $this->photographer_id],
                ['total_points' => 0]
            );
            $stats->increment('total_points', $this->achievement->points);
            $stats->updateLevel();

            // Send notification
            \App\Services\PhotographerNotificationService::sendCustomNotification(
                $this->photographer_id,
                '🏆 Achievement Unlocked!',
                "You've earned the '{$this->achievement->name}' badge and {$this->achievement->points} points!",
                [
                    'achievement_id' => $this->achievement_id,
                    'points' => $this->achievement->points,
                ],
                '/dashboard?tab=achievements',
                'achievement'
            );

            return true;
        }

        return false;
    }
}
