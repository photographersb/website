<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->is_admin || $this->route('event')->created_by === Auth::id());
    }

    public function rules(): array
    {
        $eventId = $this->route('event')?->id;

        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'city_id' => 'sometimes|exists:cities,id',
            'venue' => 'sometimes|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'start_datetime' => 'sometimes|date|after:now',
            'end_datetime' => 'sometimes|date|after:start_datetime',
            'event_type' => 'sometimes|in:free,paid',
            'base_price' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'booking_close_datetime' => 'sometimes|date|before:start_datetime',
            'refund_policy' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_featured' => 'boolean',
            'status' => 'in:draft,published,cancelled',
        ];
    }
}
