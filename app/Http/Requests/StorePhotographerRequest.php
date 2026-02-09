<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhotographerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        foreach (['specializations', 'favorite_hashtags', 'category_ids'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = array_values(array_filter(array_map('trim', explode(',', $data[$field]))));
            }
        }

        if (array_key_exists('city_id', $data) && $data['city_id'] === '') {
            $data['city_id'] = null;
        }

        $this->replace($data);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'username' => ['sometimes', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9_.-]+$/i'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:255'],
            'city_id' => ['nullable', 'exists:locations,id'],
            'profile_picture' => ['nullable', 'string', 'max:2048'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'specializations' => ['nullable', 'array'],
            'specializations.*' => ['string', 'max:100'],
            'favorite_hashtags' => ['nullable', 'array'],
            'favorite_hashtags.*' => ['string', 'max:50'],
            'service_area_radius' => ['nullable', 'numeric', 'min:0'],
            'website_url' => ['nullable', 'url', 'max:2048'],
            'facebook_url' => ['nullable', 'url', 'max:2048'],
            'instagram_url' => ['nullable', 'url', 'max:2048'],
            'twitter_url' => ['nullable', 'url', 'max:2048'],
            'linkedin_url' => ['nullable', 'url', 'max:2048'],
            'youtube_url' => ['nullable', 'url', 'max:2048'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'city_id.exists' => 'Selected location does not exist',
            'experience_years.integer' => 'Experience must be a valid number',
            'service_area_radius.numeric' => 'Service area radius must be a valid number',
        ];
    }
}
