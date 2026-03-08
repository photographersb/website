<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EventUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
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
            'event_type' => 'sometimes|in:workshop,photowalk,expo,seminar,meetup,webinar,exhibition,competition,other',
            'type' => 'sometimes|in:workshop,photowalk,expo,seminar,meetup,webinar,exhibition,competition,other',
            'description' => 'sometimes|nullable|string|min:10',
            'hero_image_url' => 'nullable|url',
            'hero_image_credit_name' => 'nullable|string|max:255',
            'hero_image_credit_url' => 'nullable|url|max:255',
            'banner_image' => 'nullable|url',
            'banner_image_credit_name' => 'nullable|string|max:255',
            'banner_image_credit_url' => 'nullable|url|max:255',
            'gallery_images' => 'nullable|array',

            // Dates & Times
            'event_date' => 'sometimes|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'all_day_event' => 'boolean',
            'duration_hours' => 'nullable|numeric|min:0.5',

            // Location & Venue
            'city_id' => 'sometimes|nullable|exists:locations,id',
            'location' => 'sometimes|nullable|string|max:255',
            'venue_name' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string|min:10',
            'google_map_link' => 'nullable|url',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',

            // Attendance & Pricing
            'max_attendees' => 'nullable|integer|min:1',
            'capacity' => 'nullable|integer|min:1',
            'max_tickets_per_user' => 'nullable|integer|min:1',
            'require_registration' => 'boolean',
            'is_ticketed' => 'boolean',
            'ticket_price' => 'nullable|numeric|min:0',
            'event_mode' => 'nullable|in:free,paid',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|in:BDT',

            // Organizer & Settings
            'organizer_id' => 'sometimes|nullable|exists:photographers,id',
            'created_by' => 'nullable|exists:users,id',
            'mentor_ids' => 'nullable|array',
            'mentor_ids.*' => 'integer|exists:mentors,id',
            'mentors' => 'nullable|array',
            'mentors.*.mentor_id' => 'required_with:mentors|exists:mentors,id',
            'mentors.*.role' => 'nullable|in:mentor,speaker,guest,trainer',
            'mentors.*.sort_order' => 'nullable|integer|min:0',
            'sponsors' => 'nullable|array',
            'sponsors.*.sponsor_id' => 'required_with:sponsors|exists:sponsors,id',
            'sponsors.*.tier' => 'nullable|in:title,gold,silver,bronze,support',
            'sponsors.*.sort_order' => 'nullable|integer|min:0',
            'sponsors.*.sponsored_amount' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:draft,published,cancelled,completed',
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date|after:now',
            'requirements' => 'nullable|string',
            'registration_deadline' => 'nullable|date',
            'certificates_enabled' => 'boolean',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'og_image' => 'nullable|url',
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
        $nullableFields = [
            'description',
            'hero_image_url',
            'hero_image_credit_name',
            'hero_image_credit_url',
            'banner_image',
            'banner_image_credit_name',
            'banner_image_credit_url',
            'event_end_date',
            'start_time',
            'end_time',
            'location',
            'venue_name',
            'venue_address',
            'google_map_link',
            'address',
            'latitude',
            'longitude',
            'max_attendees',
            'capacity',
            'max_tickets_per_user',
            'ticket_price',
            'price',
            'featured_until',
            'requirements',
            'registration_deadline',
            'certificate_template_id',
            'meta_title',
            'meta_description',
            'og_image',
        ];

        $normalized = [];
        foreach ($nullableFields as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $normalized[$field] = null;
            }
        }

        // Convert boolean strings
        $this->merge(array_merge($normalized, [
            'all_day_event' => filter_var($this->all_day_event, FILTER_VALIDATE_BOOLEAN),
            'require_registration' => filter_var($this->require_registration, FILTER_VALIDATE_BOOLEAN),
            'is_ticketed' => filter_var($this->is_ticketed, FILTER_VALIDATE_BOOLEAN),
            'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN),
            'certificates_enabled' => filter_var($this->certificates_enabled, FILTER_VALIDATE_BOOLEAN),
        ]));
    }
}
