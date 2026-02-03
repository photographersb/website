<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotographerOnboardingChecklist extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'photographer_onboarding_checklists';

    protected $fillable = [
        'photographer_id',
        'profile_completed',
        'profile_photo_uploaded',
        'portfolio_added',
        'phone_verified',
        'city_added',
        'years_of_experience_added',
        'hourly_rate_set',
        'bio_added',
        'social_media_added',
        'terms_accepted',
        'completed_at',
    ];

    protected $casts = [
        'profile_completed' => 'boolean',
        'profile_photo_uploaded' => 'boolean',
        'portfolio_added' => 'boolean',
        'phone_verified' => 'boolean',
        'city_added' => 'boolean',
        'years_of_experience_added' => 'boolean',
        'hourly_rate_set' => 'boolean',
        'bio_added' => 'boolean',
        'social_media_added' => 'boolean',
        'terms_accepted' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function photographer()
    {
        return $this->belongsTo(Photographer::class);
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentage()
    {
        $steps = [
            'profile_completed',
            'profile_photo_uploaded',
            'portfolio_added',
            'phone_verified',
            'city_added',
            'years_of_experience_added',
            'hourly_rate_set',
            'bio_added',
            'social_media_added',
            'terms_accepted',
        ];

        $completed = collect($steps)->filter(fn($step) => $this->{$step})->count();
        return (int)(($completed / count($steps)) * 100);
    }

    /**
     * Check if onboarding is complete
     */
    public function isComplete()
    {
        return $this->getCompletionPercentage() === 100;
    }

    /**
     * Mark onboarding as complete
     */
    public function markComplete()
    {
        $this->update([
            'completed_at' => now(),
        ]);
    }

    /**
     * Get next required step
     */
    public function getNextStep()
    {
        $steps = [
            'profile_completed' => 'Complete Profile Information',
            'profile_photo_uploaded' => 'Upload Profile Photo',
            'portfolio_added' => 'Add Portfolio Samples',
            'phone_verified' => 'Verify Phone Number',
            'city_added' => 'Select City/Location',
            'years_of_experience_added' => 'Add Years of Experience',
            'hourly_rate_set' => 'Set Hourly Rate',
            'bio_added' => 'Add Bio/About You',
            'social_media_added' => 'Add Social Media Links',
            'terms_accepted' => 'Accept Terms & Conditions',
        ];

        foreach ($steps as $step => $label) {
            if (!$this->{$step}) {
                return [
                    'step' => $step,
                    'label' => $label,
                ];
            }
        }

        return null;
    }
}
