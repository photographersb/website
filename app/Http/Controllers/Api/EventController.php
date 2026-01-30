<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\EventRsvp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    /**
     * Get events statistics (cached for 10 minutes)
     */
    public function stats()
    {
        return Cache::remember('events_stats', 600, function () {
            $totalEvents = Event::where('status', 'published')->count();
            $upcomingEvents = Event::where('status', 'published')
                ->where('event_date', '>=', now())
                ->count();
            $totalCities = Event::where('status', 'published')
                ->distinct('city_id')
                ->count('city_id');
            $totalRsvps = EventRsvp::where('rsvp_status', 'going')->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_events' => $totalEvents,
                    'upcoming_events' => $upcomingEvents,
                    'total_cities' => $totalCities,
                    'total_rsvps' => $totalRsvps,
                ],
            ]);
        });
    }

    /**
     * Get all events with filters
     */
    public function index(Request $request)
    {
        $query = Event::where('status', 'published')
            ->with(['organizer.user', 'city']);

        // Filter by city
        if ($request->has('city')) {
            $query->whereHas('city', function ($q) {
                $q->where('slug', request('city'));
            });
        }

        // Filter by event type
        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('event_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('event_date', '<=', $request->to_date);
        }

        // Sort
        $sortField = 'event_date';
        $sortDirection = 'asc';

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'date_desc':
                    $sortDirection = 'desc';
                    break;
                case 'featured':
                    $sortField = 'is_featured';
                    $sortDirection = 'desc';
                    break;
            }
        }

        $query->orderBy($sortField, $sortDirection);

        if ($sortField !== 'is_featured') {
            $query->orderBy('is_featured', 'desc');
        }

        $events = $query->paginate(12);

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
     * Get single event
     */
    public function show($slug)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found',
            ], 404);
        }

        if ($event->status !== 'published' && auth()->user()?->id !== $event->organizer->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found',
            ], 404);
        }

        $event->load(['organizer.user', 'city']);
        $event->increment('view_count');

        return response()->json([
            'status' => 'success',
            'data' => $event,
        ]);
    }

    /**
     * Check user's RSVP status for an event
     */
    public function rsvpStatus($eventId)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'is_rsvped' => false,
                ],
            ]);
        }

        $rsvp = EventRsvp::where('event_id', $eventId)
            ->where('user_id', auth()->id())
            ->where('rsvp_status', 'going')
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'is_rsvped' => $rsvp !== null,
            ],
        ]);
    }

    /**
     * RSVP to event
     */
    public function rsvp(Request $request, $eventId)
    {
        // Support both numeric ID and slug
        $event = is_numeric($eventId)
            ? Event::findOrFail($eventId)
            : Event::where('slug', $eventId)->firstOrFail();

        $validated = $request->validate([
            'rsvp_status' => 'required|in:going,maybe,not_going',
        ]);

        // Check capacity
        if ($event->max_attendees && $event->rsvp_count >= $event->max_attendees) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event is full',
            ], 422);
        }

        try {
            $rsvp = DB::transaction(function () use ($event, $validated) {
                $rsvp = EventRsvp::updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'user_id' => auth()->id(),
                    ],
                    [
                        'rsvp_status' => $validated['rsvp_status'],
                        'responded_at' => now(),
                    ]
                );

                // Update event RSVP count
                $event->rsvp_count = EventRsvp::where('event_id', $event->id)
                    ->where('rsvp_status', 'going')
                    ->count();
                $event->save();

                return $rsvp;
            });

            Log::info('RSVP updated successfully', [
                'event_id' => $event->id,
                'user_id' => auth()->id(),
                'rsvp_status' => $validated['rsvp_status'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'RSVP updated',
                'data' => $rsvp,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update RSVP', [
                'event_id' => $event->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update RSVP. Please try again.',
            ], 500);
        }
    }
}
