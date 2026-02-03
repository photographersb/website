<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Photographer;
use App\Models\PhotographerOnboardingChecklist;
use Illuminate\Http\Request;

class PhotographerOnboardingController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/photographer/onboarding/checklist
     * Get onboarding checklist for current photographer
     */
    public function getChecklist()
    {
        $photographer = auth()->user()->photographer;

        if (!$photographer) {
            return $this->error('Not a photographer', 403);
        }

        $checklist = $photographer->onboardingChecklist ?? PhotographerOnboardingChecklist::create([
            'photographer_id' => $photographer->id,
        ]);

        return $this->success([
            'checklist' => $checklist,
            'completion_percentage' => $checklist->getCompletionPercentage(),
            'next_step' => $checklist->getNextStep(),
            'is_complete' => $checklist->isComplete(),
        ], 'Onboarding checklist retrieved');
    }

    /**
     * PUT /api/v1/photographer/onboarding/checklist/update-step
     * Update specific onboarding step
     */
    public function updateStep(Request $request)
    {
        $validated = $request->validate([
            'step' => 'required|string|in:profile_completed,profile_photo_uploaded,portfolio_added,phone_verified,city_added,years_of_experience_added,hourly_rate_set,bio_added,social_media_added,terms_accepted',
            'completed' => 'required|boolean',
        ]);

        $photographer = auth()->user()->photographer;

        if (!$photographer) {
            return $this->error('Not a photographer', 403);
        }

        $checklist = $photographer->onboardingChecklist ?? PhotographerOnboardingChecklist::create([
            'photographer_id' => $photographer->id,
        ]);

        $checklist->update([
            $validated['step'] => $validated['completed'],
        ]);

        if ($checklist->isComplete() && !$checklist->completed_at) {
            $checklist->markComplete();
        }

        return $this->success([
            'checklist' => $checklist,
            'completion_percentage' => $checklist->getCompletionPercentage(),
            'next_step' => $checklist->getNextStep(),
            'is_complete' => $checklist->isComplete(),
        ], 'Onboarding step updated');
    }

    /**
     * GET /api/v1/photographer/onboarding/progress
     * Get onboarding progress summary
     */
    public function getProgress()
    {
        $photographer = auth()->user()->photographer;

        if (!$photographer) {
            return $this->error('Not a photographer', 403);
        }

        $checklist = $photographer->onboardingChecklist ?? PhotographerOnboardingChecklist::create([
            'photographer_id' => $photographer->id,
        ]);

        return $this->success([
            'completion_percentage' => $checklist->getCompletionPercentage(),
            'completed_steps' => $checklist->getAttributes(),
            'next_step' => $checklist->getNextStep(),
            'is_complete' => $checklist->isComplete(),
            'completed_at' => $checklist->completed_at,
        ], 'Onboarding progress retrieved');
    }

    /**
     * POST /api/v1/admin/photographer/{photographer}/onboarding/reset
     * Admin: Reset photographer onboarding checklist
     */
    public function resetChecklist(Photographer $photographer)
    {
        $this->authorize('isAdmin');

        $checklist = $photographer->onboardingChecklist;

        if ($checklist) {
            $checklist->update([
                'profile_completed' => false,
                'profile_photo_uploaded' => false,
                'portfolio_added' => false,
                'phone_verified' => false,
                'city_added' => false,
                'years_of_experience_added' => false,
                'hourly_rate_set' => false,
                'bio_added' => false,
                'social_media_added' => false,
                'terms_accepted' => false,
                'completed_at' => null,
            ]);
        }

        return $this->success([], 'Onboarding checklist reset');
    }

    /**
     * GET /api/v1/admin/photographers/onboarding/pending
     * Admin: Get photographers with incomplete onboarding
     */
    public function getPendingOnboardings()
    {
        $this->authorize('isAdmin');

        $photographers = Photographer::whereHas('onboardingChecklist', function ($query) {
            $query->whereNull('completed_at');
        })
            ->with('onboardingChecklist')
            ->paginate(50);

        return $this->paginated($photographers, 'Pending onboardings retrieved');
    }
}
