<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();
        return in_array($user->role, ['admin', 'super_admin', 'moderator']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $isDraft = $this->input('status') === 'draft';

        return [
            // Basic Information
            'title' => 'sometimes|string|max:255',
            'event_type' => 'sometimes|in:workshop,exhibition,meetup,competition,seminar,other',
            'description' => 'sometimes|string|min:10',
            'hero_image_url' => 'nullable|url',

            // Dates & Times
            'event_date' => 'sometimes|date|after_or_equal:today',
            'event_end_date' => 'nullable|date|after_or_equal:event_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'all_day_event' => 'boolean',
            'duration_hours' => 'nullable|numeric|min:0.5',

            // Location & Venue
            'city_id' => 'sometimes|exists:cities,id',
            'location' => 'sometimes|string|max:255',
            'venue_name' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string|min:10',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',

            // Attendance & Pricing
            'max_attendees' => 'nullable|integer|min:1',
            'require_registration' => 'boolean',
            'is_ticketed' => 'boolean',
            'ticket_price' => 'nullable|numeric|min:0',

            // Organizer & Settings
            'organizer_id' => 'sometimes|exists:photographers,id',
            'mentor_ids' => 'nullable|array',
            'mentor_ids.*' => 'integer|exists:mentors,id',
            'status' => 'sometimes|in:draft,published,cancelled,completed',
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date|after:now',
            'requirements' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.max' => 'Event title must not exceed 255 characters',
            'event_type.in' => 'Selected event type is invalid',
            'description.min' => 'Event description must be at least 10 characters',
            'hero_image_url.url' => 'Hero image must be a valid URL',
            'event_date.after_or_equal' => 'Event date must be today or in the future',
            'event_end_date.after_or_equal' => 'Event end date must be after start date',
            'start_time.date_format' => 'Start time must be in HH:MM format',
            'end_time.date_format' => 'End time must be in HH:MM format',
            'end_time.after_or_equal' => 'End time must be after start time',
            'duration_hours.min' => 'Duration must be at least 0.5 hours',
            'city_id.exists' => 'Selected city does not exist',
            'location.max' => 'Location name must not exceed 255 characters',
            'venue_address.min' => 'Venue address must be at least 10 characters',
            'organizer_id.exists' => 'Selected photographer does not exist',
            'ticket_price.numeric' => 'Ticket price must be a valid number',
            'ticket_price.min' => 'Ticket price cannot be negative',
            'featured_until.after' => 'Featured until date must be in the future',
            'max_attendees.min' => 'Max attendees must be at least 1',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert boolean strings
        $this->merge([
            'all_day_event' => filter_var($this->all_day_event, FILTER_VALIDATE_BOOLEAN),
            'require_registration' => filter_var($this->require_registration, FILTER_VALIDATE_BOOLEAN),
            'is_ticketed' => filter_var($this->is_ticketed, FILTER_VALIDATE_BOOLEAN),
            'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
