<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class EventApiController extends Controller
{
    /**
     * Get all published events with filtering
     */
    public function index(Request $request)
    {
        $columns = [
            'id',
            'slug',
            'title',
            'description',
            'city_id',
            'organizer_id',
            'event_type',
            'event_mode',
            'price',
            'ticket_price',
            'is_ticketed',
            'event_date',
            'event_end_date',
            'start_time',
            'end_time',
            'max_attendees',
            'capacity',
            'location',
            'location_text',
            'venue',
            'venue_name',
            'hero_image_url',
            'banner_image',
            'is_featured',
            'created_at',
        ];

        $columns = array_values(array_filter($columns, function ($column) {
            return Schema::hasColumn('events', $column);
        }));

        $query = Event::where('status', 'published')
            ->with([
                'city',
                'organizer' => function ($q) {
                    $q->select('id', 'user_id', 'slug')
                      ->with('user:id,name');
                },
                'mentors:id,name',
                'sponsors',
            ])
            ->withCount(['registrations as rsvp_count'])
            ->select($columns);

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
        } elseif ($request->filled('event_type')) {
            $query->where('event_type', $request->input('event_type'));
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('event_date', '>=', $request->input('start_date'));
        } elseif ($request->filled('from_date')) {
            $query->where('event_date', '>=', $request->input('from_date'));
        }

        if ($request->filled('end_date')) {
            $query->where('event_end_date', '<=', $request->input('end_date'));
        } elseif ($request->filled('to_date')) {
            $query->where('event_end_date', '<=', $request->input('to_date'));
        }

        // Filter featured only
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'date_asc':
            case 'soonest':
                $query->orderBy('event_date', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('event_date', 'desc');
                break;
            case 'featured':
                $query->orderByDesc('is_featured')
                    ->orderByDesc('created_at');
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
    public function show(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->first();
        
        if (!$event || $event->status !== 'published') {
            return response()->json(['error' => 'Event not found'], 404);
        }

        $event->load(['city', 'mentors', 'category', 'organizer.user', 'registrations', 'sponsors']);
        if (Schema::hasTable('event_tickets')) {
            $event->load(['tickets' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('price');
            }]);
        }

        // Calculate stats
        $registeredCount = $event->registrations()->count();
        $capacityFull = $event->max_attendees && $registeredCount >= $event->max_attendees;
        $capacityPercent = $event->max_attendees ? min(round(($registeredCount / $event->max_attendees) * 100), 100) : 0;

        $userRegistration = null;
        $user = Auth::guard('sanctum')->user() ?: Auth::user();
        if ($user) {
            $userRegistration = $event->getUserRegistration($user->id);
        }

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
                'type' => $event->type,
                'event_mode' => $event->event_mode,
                'is_ticketed' => $event->is_ticketed,
                'ticket_price' => $event->ticket_price,
                'base_price' => $event->base_price,
                'price' => $event->price,
                'currency' => $event->currency,
                'max_tickets_per_user' => $event->max_tickets_per_user,
                'registration_deadline' => $event->registration_deadline,
                'google_map_link' => $event->google_map_link,
                'event_date' => $event->event_date,
                'event_end_date' => $event->event_end_date,
                'start_datetime' => $event->start_datetime,
                'end_datetime' => $event->end_datetime,
                'max_attendees' => $event->max_attendees,
                'capacity' => $event->capacity,
                'registered_count' => $registeredCount,
                'capacity_full' => $capacityFull,
                'capacity_percent' => $capacityPercent,
                'hero_image_url' => $event->hero_image_url,
                'hero_image_credit_name' => $event->hero_image_credit_name,
                'hero_image_credit_url' => $event->hero_image_credit_url,
                'banner_image' => $event->banner_image,
                'banner_image_credit_name' => $event->banner_image_credit_name,
                'banner_image_credit_url' => $event->banner_image_credit_url,
                'gallery_images' => $event->gallery_images,
                'requirements' => $event->requirements,
                'refund_policy' => $event->refund_policy,
                'certificates_enabled' => $event->certificates_enabled,
                'mentors' => $event->mentors,
                'sponsors' => $event->sponsors,
                'tickets' => $event->relationLoaded('tickets') ? $event->tickets : [],
                'is_featured' => $event->is_featured,
                'organizer' => $event->organizer,
                'user_registration' => $userRegistration,
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
            ->where('is_featured', true)
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
                'total_cities' => Location::has('events')->count(),
                'free_events' => Event::where('status', 'published')
                    ->where('event_mode', 'free')
                    ->count(),
                'paid_events' => Event::where('status', 'published')
                    ->where('event_mode', 'paid')
                    ->count(),
            ],
        ]);
    }

    /**
     * Get all cities for filtering
     */
    public function cities()
    {
        $cities = Location::has('events')
            ->withCount('events')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }
}
