<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    use ApiResponse;
    /**
     * Get all published events with pagination
     */
    public function index(Request $request): JsonResponse
    {
        // Enforce pagination limits to prevent DoS
        $perPage = min($request->get('per_page', 15), 100);
        $page = $request->get('page', 1);

        $query = Event::published()
            ->with(['category', 'city', 'organizer']);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by city
        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('event_type', $request->type);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Featured scope ordering
        $featuredScope = $request->get('featured_scope', 'global');
        $now = now();

        if ($featuredScope === 'area' && $request->filled('city_id')) {
            $cityId = $request->city_id;
            $query->orderByRaw(
                "CASE WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) AND city_id = ? THEN 2 WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) THEN 1 ELSE 0 END DESC",
                [$now, $cityId, $now]
            );
        } elseif ($featuredScope === 'category' && $request->filled('category_id')) {
            $categoryId = $request->category_id;
            $query->orderByRaw(
                "CASE WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) AND category_id = ? THEN 2 WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) THEN 1 ELSE 0 END DESC",
                [$now, $categoryId, $now]
            );
        } else {
            $query->orderByRaw(
                "CASE WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) THEN 1 ELSE 0 END DESC",
                [$now]
            );
        }

        // Filter upcoming vs past
        if ($request->input('filter') === 'upcoming') {
            $query->upcoming();
        } elseif ($request->input('filter') === 'past') {
            $query->past();
        }

        $query->orderBy('event_date', 'asc');

        $events = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginated($events, 'Events retrieved successfully');
    }

    /**
     * Get featured events
     */
    public function featured(Request $request): JsonResponse
    {
        $query = Event::published()
            ->featured()
            ->with(['category', 'city', 'organizer'])
            ->orderBy('event_date', 'asc')
            ->take(6);

        $events = $query->get();

        return $this->success($events, 'Featured events retrieved successfully');
    }

    /**
     * Get event by slug
     */
    public function show($slug): JsonResponse
    {
        $relations = [
            'category',
            'city',
            'organizer',
        ];

        if (Schema::hasTable('event_rsvps')) {
            $relations['rsvps'] = function ($q) {
                $q->where('rsvp_status', 'going')->orWhere('rsvp_status', 'maybe');
            };
        }

        if (Schema::hasTable('event_tickets')) {
            $relations[] = 'tickets';
        }

        $event = Event::with($relations)
        ->bySlug($slug)
        ->firstOrFail();

        // Check if published (or admin can view drafts)
        if (!$event->isPublished() && !Auth::check()) {
            abort(404);
        }

        if (!$event->isPublished() && Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized to view this event');
        }

        $userRegistration = null;
        if (Auth::check() && Schema::hasTable('event_rsvps')) {
            $userRegistration = $event->getUserRegistration(Auth::id());
        }

        return $this->success([
            'event' => $event,
            'user_registration' => $userRegistration,
            'schema' => $this->generateEventSchema($event),
        ], 'Event retrieved successfully');
    }

    /**
     * RSVP to a free event
     */
    public function rsvp(Request $request, Event $event): JsonResponse
    {
        if (!Auth::check()) {
            return $this->unauthorized('Unauthorized');
        }

        // Check if event is free
        if ($event->is_paid) {
            return $this->error('Please purchase tickets for this paid event', 400);
        }

        // Check booking window
        if (!$event->isBookingOpen()) {
            return $this->error('Booking window for this event has closed', 400);
        }

        // Get or create registration
        $existing = $event->getUserRegistration(Auth::id());
        
        if ($existing) {
            // Toggle RSVP status
            if ($existing->rsvp_status === 'going') {
                $existing->update([
                    'rsvp_status' => 'not_going',
                    'responded_at' => now(),
                ]);
                $message = 'RSVP cancelled';
            } else {
                $existing->update([
                    'rsvp_status' => 'going',
                    'responded_at' => now(),
                ]);
                $message = 'RSVP updated to going';
            }
            $registration = $existing->fresh();
        } else {
            // Check capacity for new registration
            if ($event->capacity && !$event->hasCapacityFor(1)) {
                return $this->error('Not enough seats available', 400);
            }

            // Create new registration
            $registration = $event->registrations()->create([
                'user_id' => Auth::id(),
                'rsvp_status' => 'going',
                'responded_at' => now(),
            ]);
            $message = 'RSVP successful';
        }

        return $this->success($registration->load('event'), $message);
    }

    /**
     * Get user's event registrations
     */
    public function myEvents(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return $this->unauthorized('Unauthorized');
        }

        $registrations = EventRegistration::with('event', 'ticket')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return $this->paginated($registrations, 'User events retrieved successfully');
    }

    /**
     * Generate Event schema.org JSON-LD
     */
    private function generateEventSchema(Event $event): array
    {
        $offers = [];
        if (Schema::hasTable('event_tickets')) {
            $offers = $event->tickets->map(function ($ticket) use ($event) {
                return [
                    '@type' => 'Offer',
                    'url' => route('events.show', $event->slug),
                    'price' => $ticket->price,
                    'priceCurrency' => 'BDT',
                    'availability' => $ticket->getAvailableQuantity() > 0
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                    'validFrom' => $ticket->sales_start_datetime->toIso8601String(),
                ];
            })->toArray();
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event->title,
            'description' => $event->description,
            'image' => $event->hero_image_url ?? $event->banner_image,
            'startDate' => optional($event->event_date)->toIso8601String(),
            'endDate' => optional($event->event_end_date)->toIso8601String(),
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'location' => [
                '@type' => 'Place',
                'name' => $event->location ?? $event->venue ?? $event->location_text,
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $event->city?->name ?? '',
                    'addressCountry' => 'BD',
                ],
            ],
            'organizer' => [
                '@type' => 'Organization',
                'name' => $event->organizer?->name ?? 'Event Organizer',
            ],
            'offers' => $offers,
        ];
    }

    /**
     * Get event statistics
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_events' => Event::where('status', 'published')->count(),
            'upcoming_events' => Event::where('status', 'published')
                ->where('event_date', '>=', now())
                ->count(),
            'total_cities' => Event::where('status', 'published')
                ->distinct('city_id')
                ->whereNotNull('city_id')
                ->count('city_id'),
            'total_rsvps' => Event::where('status', 'published')->sum('rsvp_count'),
        ];

        return $this->success($stats, 'Event stats retrieved successfully');
    }
}
