<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SiteLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'title',
        'url',
        'icon',
        'route_name',
        'open_in_new_tab',
        'sort_order',
        'is_active',
        'visibility',
        'created_by_user_id',
    ];

    protected $casts = [
        'open_in_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Valid section types
     */
    public const SECTIONS = [
        'navbar' => 'Navigation Bar',
        'footer_company' => 'Footer - Company',
        'footer_legal' => 'Footer - Legal',
        'footer_useful' => 'Footer - Useful Links',
        'social' => 'Social Media',
        'cta' => 'Call to Action',
    ];

    /**
     * Valid visibility options
     */
    public const VISIBILITY_OPTIONS = [
        'public' => 'Everyone',
        'guest_only' => 'Guests Only (Not Logged In)',
        'auth_only' => 'Authenticated Users Only',
    ];

    /**
     * Get the user who created this link
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Scope: Active links only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: By section
     */
    public function scopeBySection($query, string $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope: Ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Scope: By visibility (considering auth state)
     */
    public function scopeVisibleTo($query, bool $isAuthenticated)
    {
        return $query->where(function ($q) use ($isAuthenticated) {
            $q->where('visibility', 'public');
            
            if ($isAuthenticated) {
                $q->orWhere('visibility', 'auth_only');
            } else {
                $q->orWhere('visibility', 'guest_only');
            }
        });
    }

    /**
     * Get the final URL (prefer route_name if set, otherwise use url)
     */
    public function getFinalUrlAttribute(): string
    {
        if (!empty($this->route_name)) {
            try {
                return route($this->route_name);
            } catch (\Exception $e) {
                // Fallback to URL if route doesn't exist
                return $this->url;
            }
        }

        return $this->url;
    }

    /**
     * Get target attribute for links
     */
    public function getTargetAttribute(): string
    {
        return $this->open_in_new_tab ? '_blank' : '_self';
    }

    /**
     * Get rel attribute for external links
     */
    public function getRelAttribute(): string
    {
        return $this->open_in_new_tab ? 'noopener noreferrer' : '';
    }

    /**
     * Boot method - add validation
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->validateBeforeSave();
        });
    }

    /**
     * Validate before saving (prevent XSS, validate URLs)
     */
    protected function validateBeforeSave()
    {
        $validator = Validator::make($this->attributes, [
            'title' => 'required|string|max:255',
            'url' => 'required_without:route_name|nullable|string|max:2000',
            'route_name' => 'nullable|string|max:255',
            'section' => 'required|in:' . implode(',', array_keys(self::SECTIONS)),
            'visibility' => 'required|in:' . implode(',', array_keys(self::VISIBILITY_OPTIONS)),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Security: Prevent javascript: URLs
        if (!empty($this->url) && preg_match('/^javascript:/i', $this->url)) {
            throw new \InvalidArgumentException('JavaScript URLs are not allowed for security reasons.');
        }

        // Security: Prevent data: URLs
        if (!empty($this->url) && preg_match('/^data:/i', $this->url)) {
            throw new \InvalidArgumentException('Data URLs are not allowed for security reasons.');
        }
    }

    /**
     * Get section label
     */
    public function getSectionLabelAttribute(): string
    {
        return self::SECTIONS[$this->section] ?? $this->section;
    }

    /**
     * Get visibility label
     */
    public function getVisibilityLabelAttribute(): string
    {
        return self::VISIBILITY_OPTIONS[$this->visibility] ?? $this->visibility;
    }
}
