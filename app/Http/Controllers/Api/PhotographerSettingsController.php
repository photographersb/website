<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\ApiResponse;
use App\Models\City;
use App\Models\Photographer;
use Illuminate\Http\Request;

class PhotographerSettingsController extends Controller
{
    use ApiResponse;
    /**
     * Get photographer settings
     */
    public function getSettings()
    {
        $user = auth()->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->error('You are not a photographer', 403);
        }

        $categoryIds = $photographer->categories()->pluck('categories.id')->all();

        $locationName = $photographer->location;
        if (!$locationName) {
            $locationName = optional($photographer->city)->name;
        }
        if (!$locationName && $photographer->city_id) {
            $locationName = City::find($photographer->city_id)?->name;
        }

        return $this->success([
            'bio' => $photographer->bio,
            'short_bio' => $photographer->short_bio,
            'location' => $locationName,
            'city_id' => $photographer->city_id,
            'profile_picture' => $photographer->profile_picture,
            'experience_years' => $photographer->experience_years,
            'specializations' => $photographer->specializations,
            'favorite_hashtags' => $photographer->favorite_hashtags,
            'category_ids' => $categoryIds,
            'service_area_radius' => $photographer->service_area_radius,
            'accept_tips' => $photographer->accept_tips,
            'tip_phone_number' => $photographer->tip_phone_number,
            'bkash_number' => $photographer->bkash_number,
            'nagad_number' => $photographer->nagad_number,
            'rocket_number' => $photographer->rocket_number,
            'tip_message' => $photographer->tip_message,
            'facebook_url' => $photographer->facebook_url,
            'instagram_url' => $photographer->instagram_url,
            'twitter_url' => $photographer->twitter_url,
            'linkedin_url' => $photographer->linkedin_url,
            'youtube_url' => $photographer->youtube_url,
            'website_url' => $photographer->website_url,
            'pexels_url' => $photographer->pexels_url,
            'is_available' => $photographer->is_available,
            'response_time_preference' => $photographer->response_time_preference,
            'booking_lead_time' => $photographer->booking_lead_time,
        ]);
    }

    /**
     * Update profile settings
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string|max:500',
            'short_bio' => 'nullable|string|max:200',
            'location' => 'nullable|string|max:255',
            'city_id' => 'nullable|exists:locations,id',
            'profile_picture' => 'nullable|file|mimes:jpeg,png,webp,jpg|max:5120',
            'experience_years' => 'nullable|integer|min:0|max:60',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:100',
            'favorite_hashtags' => 'nullable|array',
            'favorite_hashtags.*' => 'string|max:50',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'service_area_radius' => 'nullable|numeric|min:0|max:500',
        ]);

        $user = auth()->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->error('You are not a photographer', 403);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $path = $file->store('profile-pictures/' . $photographer->id, 'public');
            // Store relative path - accessor will add /storage/ prefix
            $request->merge(['profile_picture' => $path]);
        }

        $photographer->update($request->only([
            'bio',
            'short_bio',
            'location',
            'city_id',
            'profile_picture',
            'experience_years',
            'specializations',
            'favorite_hashtags',
            'service_area_radius',
        ]));

        if ($request->has('category_ids')) {
            $photographer->categories()->sync($request->category_ids);
        }

        return $this->success([
            'message' => 'Profile updated successfully',
            'photographer' => $photographer,
        ]);
    }

    /**
     * Update tip settings
     */
    public function updateTips(Request $request)
    {
        $request->validate([
            'accept_tips' => 'nullable|boolean',
            'tip_phone_number' => 'nullable|string|max:20',
            'bkash_number' => 'nullable|string|max:20',
            'nagad_number' => 'nullable|string|max:20',
            'rocket_number' => 'nullable|string|max:20',
            'tip_message' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->error('You are not a photographer', 403);
        }

        $acceptTips = $request->boolean('accept_tips');
        $legacyNumber = $request->input('tip_phone_number');

        $bkashNumber = $request->input('bkash_number') ?: $legacyNumber;
        $nagadNumber = $request->input('nagad_number');
        $rocketNumber = $request->input('rocket_number');

        $hasNumber = !empty($bkashNumber) || !empty($nagadNumber) || !empty($rocketNumber)
            || !empty($photographer->bkash_number) || !empty($photographer->nagad_number) || !empty($photographer->rocket_number);

        if ($acceptTips && !$hasNumber) {
            return $this->error('Please add at least one payment number for tips.', 422);
        }

        $numbersToValidate = [
            'bKash' => $bkashNumber,
            'Nagad' => $nagadNumber,
            'Rocket' => $rocketNumber,
        ];

        foreach ($numbersToValidate as $label => $number) {
            if (!empty($number) && !preg_match('/^(\+880|880|0)[0-9]{9,10}$/', str_replace([' ', '-'], '', $number))) {
                return $this->error('Invalid ' . $label . ' number format. Please use Bangladesh format (e.g., +880xxxxxxxxxx)', 422);
            }
        }

        $primaryNumber = $bkashNumber ?: ($nagadNumber ?: ($rocketNumber ?: null));

        $photographer->update([
            'accept_tips' => $acceptTips,
            'bkash_number' => $bkashNumber,
            'nagad_number' => $nagadNumber,
            'rocket_number' => $rocketNumber,
            'tip_phone_number' => $primaryNumber,
            'tip_message' => $request->input('tip_message'),
        ]);

        return $this->success([
            'message' => 'Tip settings updated successfully',
            'photographer' => $photographer,
        ]);
    }

    /**
     * Update social media links
     */
    public function updateSocial(Request $request)
    {
        $request->validate([
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'pexels_url' => 'nullable|url|max:255',
        ]);

        $user = auth()->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->error('You are not a photographer', 403);
        }

        $photographer->update($request->only([
            'facebook_url',
            'instagram_url',
            'twitter_url',
            'linkedin_url',
            'youtube_url',
            'website_url',
            'pexels_url',
        ]));

        return $this->success([
            'message' => 'Social links updated successfully',
            'photographer' => $photographer,
        ]);
    }

    /**
     * Update availability settings
     */
    public function updateAvailability(Request $request)
    {
        $request->validate([
            'is_available' => 'nullable|boolean',
            'response_time_preference' => 'nullable|in:under_1_hour,1_to_3_hours,3_to_24_hours,over_24_hours',
            'booking_lead_time' => 'nullable|integer|min:0|max:365',
        ]);

        $user = auth()->user();
        $photographer = $user->photographer;

        if (!$photographer) {
            return $this->error('You are not a photographer', 403);
        }

        $photographer->update($request->only([
            'is_available',
            'response_time_preference',
            'booking_lead_time',
        ]));

        return $this->success([
            'message' => 'Availability settings updated successfully',
            'photographer' => $photographer,
        ]);
    }
}
