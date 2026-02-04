<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class UserVerification extends Model
{
    protected $fillable = [
        'user_id',
        'is_verified',
        'verified_at',
        'verified_by_user_id',
        'verification_level'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    /**
     * Scopes
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified(Builder $query): Builder
    {
        return $query->where('is_verified', false);
    }

    public function scopeBasicLevel(Builder $query): Builder
    {
        return $query->where('verification_level', 'basic');
    }

    public function scopeFullLevel(Builder $query): Builder
    {
        return $query->where('verification_level', 'full');
    }

    /**
     * Helper Methods
     */
    public function isVerified(): bool
    {
        return $this->is_verified === true;
    }

    public function isBasicLevel(): bool
    {
        return $this->verification_level === 'basic';
    }

    public function isFullLevel(): bool
    {
        return $this->verification_level === 'full';
    }

    public function getVerificationBadge(): string
    {
        if (!$this->is_verified) {
            return '';
        }

        return $this->verification_level === 'full' ? '✓✓' : '✓';
    }

    /**
     * Get verification level label
     */
    public function getLevelLabel(): string
    {
        return match($this->verification_level) {
            'basic' => 'Basic Verification',
            'full' => 'Full Verification',
            default => 'Unknown'
        };
    }
}
