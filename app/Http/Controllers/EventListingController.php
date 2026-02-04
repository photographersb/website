<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\City;
use Illuminate\Http\Request;

class EventListingController extends Controller
{
    /**
     * Display list of published events
     */
    public function index(Request $request)
    {
        $query = Event::where('status', 'published')
            ->with(['city', 'mentors', 'registrations'])
            ->orderByDesc('created_at');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // City filter
        if ($request->filled('city')) {
            $query->where('city_id', $request->input('city'));
        }

        // Type filter (free/paid)
        if ($request->filled('type')) {
            $query->where('event_type', $request->input('type'));
        }

        // Date range filter
        if ($request->filled('when')) {
            $when = $request->input('when');
            $now = now();

            if ($when === 'upcoming') {
                $query->where('start_datetime', '>=', $now);
            } elseif ($when === 'this_week') {
                $query->whereBetween('start_datetime', [
                    $now,
                    $now->copy()->endOfWeek()
                ]);
            } elseif ($when === 'this_month') {
                $query->whereBetween('start_datetime', [
                    $now,
                    $now->copy()->endOfMonth()
                ]);
            }
        }

        // Price filter
        if ($request->filled('price')) {
            $price = $request->input('price');
            
            if ($price === 'free') {
                $query->where('event_type', 'free');
            } elseif ($price === 'under_1000') {
                $query->where('event_type', 'paid')
                      ->where('price', '<', 1000);
            } elseif ($price === '1000_5000') {
                $query->where('event_type', 'paid')
                      ->whereBetween('price', [1000, 5000]);
            } elseif ($price === 'over_5000') {
                $query->where('event_type', 'paid')
                      ->where('price', '>', 5000);
            }
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        
        switch ($sort) {
            case 'soonest':
                $query->orderBy('start_datetime', 'asc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderByDesc('price');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
        }

        // Pagination
        $events = $query->paginate(12);

        // Get all cities for filter dropdown
        $cities = City::orderBy('name')->get();

        return view('events.index', [
            'events' => $events,
            'cities' => $cities,
        ]);
    }

    /**
     * User's registration dashboard
     */
    public function myRegistrations(Request $request)
    {
        $user = auth()->user();

        $registrations = $user->eventRegistrations()
            ->with(['event', 'event.city'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->input('status');
                if ($status === 'upcoming') {
                    $q->whereHas('event', function ($eq) {
                        $eq->where('start_datetime', '>=', now());
                    });
                } elseif ($status === 'past') {
                    $q->whereHas('event', function ($eq) {
                        $eq->where('start_datetime', '<', now());
                    });
                } elseif ($status === 'attended') {
                    $q->whereHas('attendanceLogs');
                }
            })
            ->orderByDesc('registered_at')
            ->paginate(12);

        return view('events.my-registrations', [
            'registrations' => $registrations,
        ]);
    }

    /**
     * Featured events widget (homepage)
     */
    public function featured()
    {
        $events = Event::where('status', 'published')
            ->where('featured', true)
            ->with(['city', 'registrations'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        return view('events.featured', [
            'events' => $events,
        ]);
    }
}
