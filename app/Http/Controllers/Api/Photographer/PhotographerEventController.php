<?php

namespace App\Http\Controllers\Api\Photographer;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Event;
use App\Models\Photographer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PhotographerEventController extends Controller
{
    use ApiResponse;
    /**
     * Check if user is a verified photographer
     */
    private function checkVerifiedPhotographer()
    {
        $photographer = Photographer::where('user_id', Auth::id())->first();

        if (!$photographer) {
            abort(403, 'You must have a photographer profile to create events');
        }

        return $photographer;
    }

    /**
     * Get photographer's events
     */
    public function index(Request $request)
    {
        $photographer = Photographer::where('user_id', Auth::id())->first();

        if (!$photographer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Photographer profile not found',
            ], 404);
        }

        $query = Event::where('organizer_id', $photographer->id)
            ->with(['city']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $query->orderBy('event_date', 'desc');

        $events = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $events->items(),
            'meta' => [
                'total' => $events->total(),
                'per_page' => $events->perPage(),
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
            ],
        ]);
    }

    /**
     * Create new event (photographer)
     */
    public function store(Request $request)
    {
        $photographer = $this->checkVerifiedPhotographer();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'event_type' => 'required|in:workshop,exhibition,meetup,competition,seminar,other',
            'hero_image_url' => 'nullable|url',
            'event_date' => 'required|date|after:now',
            'event_end_date' => 'nullable|date|after:event_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'all_day_event' => 'boolean',
            'location' => 'required|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'city_id' => 'required|exists:cities,id',
            'max_attendees' => 'nullable|integer|min:1|max:500',
            'require_registration' => 'boolean',
            'is_ticketed' => 'boolean',
            'ticket_price' => 'nullable|numeric|min:0|max:50000',
            'requirements' => 'nullable|string',
            'duration_hours' => 'nullable|numeric|min:0.5|max:24',
        ]);

        // Photographer restrictions
        $validated['organizer_id'] = $photographer->id;
        $validated['status'] = 'draft'; // Requires admin approval
        $validated['is_featured'] = false;
        $validated['is_verified'] = false;

        // Generate slug
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $count = 1;

        while (Event::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $validated['slug'] = $slug;

        $event = Event::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Event created successfully. Pending admin approval.',
            'data' => $event->load(['organizer.user', 'city']),
        ], 201);
    }

    /**
     * Get single event
     */
    public function show($id)
    {
        $photographer = $this->checkVerifiedPhotographer();

        $event = Event::where('id', $id)
            ->where('organizer_id', $photographer->id)
            ->with(['city'])
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $event,
        ]);
    }

    /**
     * Update event (only if draft or own)
     */
    public function update(Request $request, $id)
    {
        $photographer = $this->checkVerifiedPhotographer();

        $event = Event::where('id', $id)
            ->where('organizer_id', $photographer->id)
            ->firstOrFail();

        // Explicit authorization check
        $this->authorize('update', $event);

        // Can only edit draft or upcoming events
        if ($event->status === 'cancelled' || ($event->event_date < now() && $event->status === 'published')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot edit past or cancelled events',
            ], 422);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|min:50',
            'event_type' => 'sometimes|in:workshop,exhibition,meetup,competition,seminar,other',
            'hero_image_url' => 'nullable|url',
            'event_date' => 'sometimes|date|after:now',
            'event_end_date' => 'nullable|date|after:event_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'all_day_event' => 'boolean',
            'location' => 'sometimes|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'city_id' => 'sometimes|exists:cities,id',
            'max_attendees' => 'nullable|integer|min:1|max:500',
            'require_registration' => 'boolean',
            'is_ticketed' => 'boolean',
            'ticket_price' => 'nullable|numeric|min:0|max:50000',
            'requirements' => 'nullable|string',
            'duration_hours' => 'nullable|numeric|min:0.5|max:24',
        ]);

        // Regenerate slug if title changed
        if (isset($validated['title']) && $validated['title'] !== $event->title) {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $count = 1;

            while (Event::where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }

            $validated['slug'] = $slug;
        }

        // If published event is edited, set back to draft
        if ($event->status === 'published') {
            $validated['status'] = 'draft';
            $message = 'Event updated and set to draft. Awaiting admin approval.';
        } else {
            $message = 'Event updated successfully';
        }

        $event->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $event->load(['organizer.user', 'city']),
        ]);
    }

    /**
     * Delete event (only if draft or no RSVPs)
     */
    public function destroy($id)
    {
        $photographer = $this->checkVerifiedPhotographer();

        $event = Event::where('id', $id)
            ->where('organizer_id', $photographer->id)
            ->firstOrFail();

        // Explicit authorization check
        $this->authorize('delete', $event);

        // Can only delete draft events or events with no RSVPs
        if ($event->status === 'published' && ($event->rsvp_count ?? 0) > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete published event with RSVPs. Please cancel instead.',
            ], 422);
        }

        $event->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Event deleted successfully',
        ]);
    }

    /**
     * Cancel event
     */
    public function cancel($id)
    {
        $photographer = $this->checkVerifiedPhotographer();

        $event = Event::where('id', $id)
            ->where('organizer_id', $photographer->id)
            ->firstOrFail();

        // Explicit authorization check
        $this->authorize('delete', $event);

        if ($event->status === 'cancelled') {
            return response()->json([
                'status' => 'error',
                'message' => 'Event already cancelled',
            ], 422);
        }

        $event->update(['status' => 'cancelled']);

        return response()->json([
            'status' => 'success',
            'message' => 'Event cancelled successfully',
            'data' => $event,
        ]);
    }
}
