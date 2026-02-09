<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventStoreRequest extends FormRequest
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
            'title' => [Rule::requiredIf(!$isDraft), 'nullable', 'string', 'max:255'],
            'event_type' => [Rule::requiredIf(!$isDraft), 'nullable', 'in:workshop,photowalk,expo,seminar,meetup,webinar,exhibition,competition,other'],
            'type' => 'nullable|in:workshop,photowalk,expo,seminar,meetup,webinar,exhibition,competition,other',
            'description' => [Rule::requiredIf(!$isDraft), 'nullable', 'string', 'min:10'],
            'hero_image_url' => 'nullable|url',
            'hero_image_credit_name' => 'nullable|string|max:255',
            'hero_image_credit_url' => 'nullable|url|max:255',
            'banner_image' => 'nullable|url',
            'banner_image_credit_name' => 'nullable|string|max:255',
            'banner_image_credit_url' => 'nullable|url|max:255',
            'gallery_images' => 'nullable|array',

            // Dates & Times
            'event_date' => $isDraft ? ['nullable', 'date'] : ['required', 'date', 'after_or_equal:today'],
            'event_end_date' => 'nullable|date|after_or_equal:event_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'all_day_event' => 'boolean',
            'duration_hours' => 'nullable|numeric|min:0.5',

            // Location & Venue
            'city_id' => [Rule::requiredIf(!$isDraft), 'nullable', 'exists:locations,id'],
            'location' => [Rule::requiredIf(!$isDraft), 'nullable', 'string', 'max:255'],
            'venue_name' => [Rule::requiredIf(!$isDraft), 'nullable', 'string', 'max:255'],
            'venue_address' => [Rule::requiredIf(!$isDraft), 'nullable', 'string', 'min:10'],
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
            'organizer_id' => [Rule::requiredIf(!$isDraft), 'nullable', 'exists:photographers,id'],
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
            'status' => 'required|in:draft,published,cancelled',
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
            'title.required' => 'Event title is required',
            'title.max' => 'Event title must not exceed 255 characters',
            'event_type.required' => 'Event type is required',
            'event_type.in' => 'Selected event type is invalid',
            'description.required' => 'Event description is required',
            'description.min' => 'Event description must be at least 10 characters',
            'hero_image_url.url' => 'Hero image must be a valid URL',
            'event_date.required' => 'Event start date is required',
            'event_date.after_or_equal' => 'Event date must be today or in the future',
            'event_end_date.after_or_equal' => 'Event end date must be after start date',
            'start_time.date_format' => 'Start time must be in HH:MM format',
            'end_time.date_format' => 'End time must be in HH:MM format',
            'end_time.after_or_equal' => 'End time must be after start time',
            'duration_hours.min' => 'Duration must be at least 0.5 hours',
            'city_id.required' => 'City is required for published events',
            'city_id.exists' => 'Selected city does not exist',
            'location.required' => 'Location display name is required',
            'venue_name.required' => 'Venue name is required for published events',
            'venue_address.required' => 'Venue address is required for published events',
            'venue_address.min' => 'Venue address must be at least 10 characters',
            'organizer_id.required' => 'Event organizer (photographer) is required',
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
            'certificates_enabled' => filter_var($this->certificates_enabled, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
