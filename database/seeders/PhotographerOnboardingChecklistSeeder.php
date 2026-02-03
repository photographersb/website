<?php

namespace Database\Seeders;

use App\Models\Photographer;
use App\Models\PhotographerOnboardingChecklist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhotographerOnboardingChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $photographers = Photographer::all();

        foreach ($photographers as $photographer) {
            // Create onboarding checklist if not exists
            PhotographerOnboardingChecklist::firstOrCreate(
                ['photographer_id' => $photographer->id],
                [
                    'profile_completed' => !empty($photographer->bio),
                    'profile_photo_uploaded' => !empty($photographer->profile_picture),
                    'portfolio_added' => $photographer->albums()->count() > 0,
                    'phone_verified' => $photographer->user->phone_verified_at !== null,
                    'city_added' => !empty($photographer->city_id),
                    'years_of_experience_added' => !empty($photographer->experience_years),
                    'hourly_rate_set' => false,
                    'bio_added' => !empty($photographer->bio),
                    'social_media_added' => !empty($photographer->instagram_url) || 
                                          !empty($photographer->facebook_url) || 
                                          !empty($photographer->twitter_url),
                    'terms_accepted' => true,
                ]
            );
        }
    }
}
