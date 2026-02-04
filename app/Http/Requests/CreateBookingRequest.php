<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'photographer_user_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'city_id' => 'nullable|integer',
            'venue_address' => 'nullable|string|max:500',
            'event_date' => 'required|date|after_or_equal:today',
            'event_time' => 'nullable|date_format:H:i',
            'duration_hours' => 'nullable|integer|min:1|max:24',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gt_or_equal:budget_min',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'photographer_user_id.required' => 'Photographer is required.',
            'event_date.required' => 'Event date is required.',
            'event_date.after_or_equal' => 'Event date must be today or in the future.',
            'budget_max.gt_or_equal' => 'Maximum budget must be greater than or equal to minimum budget.',
        ];
    }
}
