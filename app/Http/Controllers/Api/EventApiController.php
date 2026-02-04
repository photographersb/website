<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventApiController extends Controller
{
    /**
     * Get all published events with filtering
     */
    public function index(Request $request)
    {
        $query = Event::where('status', 'published')
            ->with(['city', 'mentors:id,name,specialization', 'registrations'])
            ->select('id', 'slug', 'title', 'description', 'city_id', 'event_type', 'price', 'start_datetime', 'end_datetime', 'capacity', 'banner_image_path', 'featured', 'created_at');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->input('city_id'));
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('event_type', $request->input('type'));
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('start_datetime', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->where('end_datetime', '<=', $request->input('end_date'));
        }

        // Filter featured only
        if ($request->boolean('featured')) {
            $query->where('featured', true);
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'soonest':
                $query->orderBy('start_datetime', 'asc');
                break;
            case 'popular':
                $query->withCount('registrations')
                      ->orderByDesc('registrations_count');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
        }

        // Pagination
        $perPage = min($request->input('per_page', 15), 100);
        $events = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $events->items(),
            'pagination' => [
                'total' => $events->total(),
                'per_page' => $events->perPage(),
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'from' => $events->firstItem(),
                'to' => $events->lastItem(),
            ],
        ]);
    }

    /**
     * Get single event by slug
     */
    public function show(Event $event)
    {
        if ($event->status !== 'published') {
            return response()->json(['error' => 'Event not found'], 404);
        }

        $event->load(['city', 'mentors', 'category', 'organizer', 'registrations']);

        // Calculate stats
        $registeredCount = $event->registrations()->count();
        $capacityFull = $event->capacity && $registeredCount >= $event->capacity;
        $capacityPercent = $event->capacity ? min(round(($registeredCount / $event->capacity) * 100), 100) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $event->id,
                'slug' => $event->slug,
                'title' => $event->title,
                'description' => $event->description,
                'city' => $event->city,
                'venue_name' => $event->venue_name,
                'venue_address' => $event->venue_address,
                'event_type' => $event->event_type,
                'price' => $event->price,
                'start_datetime' => $event->start_datetime,
                'end_datetime' => $event->end_datetime,
                'capacity' => $event->capacity,
                'registered_count' => $registeredCount,
                'capacity_full' => $capacityFull,
                'capacity_percent' => $capacityPercent,
                'banner_image_path' => $event->banner_image_path,
                'requirements' => $event->requirements,
                'refund_policy' => $event->refund_policy,
                'registration_deadline' => $event->registration_deadline,
                'certificates_enabled' => $event->certificates_enabled,
                'mentors' => $event->mentors,
                'featured' => $event->featured,
                'created_at' => $event->created_at,
            ],
        ]);
    }

    /**
     * Get featured events
     */
    public function featured(Request $request)
    {
        $events = Event::where('status', 'published')
            ->where('featured', true)
            ->with(['city', 'registrations:id,event_id'])
            ->orderByDesc('created_at')
            ->limit($request->input('limit', 6))
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
    }

    /**
     * Get events statistics
     */
    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_events' => Event::where('status', 'published')->count(),
                'total_registrations' => Event::where('status', 'published')
                    ->withCount('registrations')
                    ->get()
                    ->sum('registrations_count'),
                'total_cities' => City::has('events')->count(),
                'free_events' => Event::where('status', 'published')
                    ->where('event_type', 'free')
                    ->count(),
                'paid_events' => Event::where('status', 'published')
                    ->where('event_type', 'paid')
                    ->count(),
            ],
        ]);
    }

    /**
     * Get all cities for filtering
     */
    public function cities()
    {
        $cities = City::has('events')
            ->withCount('events')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }
}
