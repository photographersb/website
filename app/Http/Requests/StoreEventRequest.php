<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:locations,id',
            'venue' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'event_type' => 'required|in:free,paid',
            'base_price' => 'required_if:event_type,paid|nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'booking_close_datetime' => 'required|date|before:start_datetime',
            'refund_policy' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_featured' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Event title is required',
            'start_datetime.after' => 'Start date must be in the future',
            'end_datetime.after' => 'End date must be after start date',
            'booking_close_datetime.before' => 'Booking must close before event starts',
            'base_price.required_if' => 'Price is required for paid events',
        ];
    }
}
