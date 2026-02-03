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
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $photographerId = $this->route('photographer')?->id;

        return [
            'user_id' => ['required_on:create', 'exists:users,id'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'city_id' => ['required', 'exists:cities,id'],
            'profile_photo_url' => ['nullable', 'url'],
            'phone' => ['nullable', 'string', 'regex:/^[\d\s\-\+\(\)]+$/'],
            'years_of_experience' => ['nullable', 'integer', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'is_verified' => ['boolean'],
            'social_media' => ['nullable', 'array'],
            'social_media.instagram' => ['nullable', 'url'],
            'social_media.facebook' => ['nullable', 'url'],
            'social_media.twitter' => ['nullable', 'url'],
            'social_media.portfolio' => ['nullable', 'url'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'city_id.required' => 'City is required',
            'city_id.exists' => 'Selected city does not exist',
            'phone.regex' => 'Invalid phone number format',
            'hourly_rate.numeric' => 'Hourly rate must be a valid number',
        ];
    }
}
