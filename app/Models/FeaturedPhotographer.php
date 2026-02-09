<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedPhotographer extends Model
{
    use HasFactory;

    protected $fillable = [
        'photographer_id',
        'package_tier',
        'category',
        'location',
        'start_date',
        'end_date',
        'active',
        'approved_by',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'active' => 'boolean',
    ];

    /**
     * Relationship: belongsTo Photographer
     */
    public function photographer()
    {
        return $this->belongsTo(Photographer::class);
    }

    /**
     * Relationship: belongs to admin user who approved
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship: hasMany payments
     */
    public function payments()
    {
        return $this->hasMany(FeaturedPhotographerPayment::class);
    }

    /**
     * Relationship: hasMany upgrades
     */
    public function upgrades()
    {
        return $this->hasMany(FeaturedPhotographerUpgrade::class);
    }

    /**
     * Check if featured listing is currently active
     */
    public function isCurrentlyActive()
    {
        if (!$this->active) {
            return false;
        }

        $now = now();
        return $this->start_date <= $now && $now <= $this->end_date;
    }

    /**
     * Scope: Active listings only
     */
    public function scopeActive($query)
    {
        return $query->where('active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Scope: By package tier
     */
    public function scopeByPackage($query, $package)
    {
        return $query->where('package_tier', $package);
    }

    /**
     * Scope: By category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: By location
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }
}
