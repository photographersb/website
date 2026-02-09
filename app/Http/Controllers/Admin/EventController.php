<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Location;
use App\Models\Category;
use App\Models\Mentor;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Show list of all events
     */
    public function index(Request $request)
    {
        $query = Event::with(['city', 'category', 'organizer']);

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('event_type', $request->type);
        }

        // Get registrations count
        $events = $query->orderBy('start_datetime', 'desc')
            ->withCount('registrations')
            ->paginate(15)
            ->withQueryString();

        return view('admin.events.index', [
            'events' => $events
        ]);
    }

    /**
     * Show create event form
     */
    public function create()
    {
        return view('admin.events.create', [
            'cities' => Location::where('is_active', true)
                ->whereIn('type', ['district', 'upazila'])
                ->orderBy('name')
                ->get(),
            'categories' => Category::orderBy('name')->get(),
            'mentors' => Mentor::orderBy('name')->get(),
            'certificateTemplates' => CertificateTemplate::orderBy('name')->get(),
        ]);
    }

    /**
     * Store new event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'organizer_id' => 'nullable|exists:photographers,id',
            'city_id' => 'required|exists:locations,id',
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'start_datetime' => 'required|date_format:Y-m-d\TH:i',
            'end_datetime' => 'required|date_format:Y-m-d\TH:i|after:start_datetime',
            'registration_deadline' => 'nullable|date_format:Y-m-d\TH:i',
            'booking_close_datetime' => 'nullable|date_format:Y-m-d\TH:i',
            'event_type' => 'required|in:free,paid',
            'price' => 'nullable|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'certificates_enabled' => 'boolean',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
            'refund_policy' => 'nullable|string',
            'requirements' => 'nullable|string',
            'banner_image' => 'nullable|image|max:5120',
            'status' => 'in:draft,published,cancelled',
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date_format:Y-m-d\TH:i',
            'mentors' => 'nullable|array',
            'mentors.*' => 'exists:mentors,id',
        ]);

        // Handle banner image
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('events', 'public');
        }

        // Set organizer and creator
        $validated['organizer_id'] = $validated['organizer_id'] ?? Auth::id();
        $validated['created_by'] = Auth::id();

        // Create slug
        $validated['slug'] = Event::generateSlug($validated['title']);

        // Create event
        $event = Event::create($validated);

        // Attach mentors if provided
        if ($request->has('mentors') && $request->mentors) {
            $event->mentors()->sync($request->mentors);
        }

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Event created successfully');
    }

    /**
     * Show event details
     */
    public function show(Event $event)
    {
        $event->load(['city', 'category', 'organizer', 'mentors', 'certificateTemplate', 'registrations']);

        return view('admin.events.show', [
            'event' => $event
        ]);
    }

    /**
     * Show edit event form
     */
    public function edit(Event $event)
    {
        $event->load(['mentors']);

        return view('admin.events.edit', [
            'event' => $event,
            'cities' => Location::where('is_active', true)
                ->whereIn('type', ['district', 'upazila'])
                ->orderBy('name')
                ->get(),
            'categories' => Category::orderBy('name')->get(),
            'mentors' => Mentor::orderBy('name')->get(),
            'certificateTemplates' => CertificateTemplate::orderBy('name')->get(),
        ]);
    }

    /**
     * Update event
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'organizer_id' => 'nullable|exists:photographers,id',
            'city_id' => 'required|exists:locations,id',
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'start_datetime' => 'required|date_format:Y-m-d\TH:i',
            'end_datetime' => 'required|date_format:Y-m-d\TH:i|after:start_datetime',
            'registration_deadline' => 'nullable|date_format:Y-m-d\TH:i',
            'booking_close_datetime' => 'nullable|date_format:Y-m-d\TH:i',
            'event_type' => 'required|in:free,paid',
            'price' => 'nullable|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'certificates_enabled' => 'boolean',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
            'refund_policy' => 'nullable|string',
            'requirements' => 'nullable|string',
            'banner_image' => 'nullable|image|max:5120',
            'status' => 'in:draft,published,cancelled',
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date_format:Y-m-d\TH:i',
            'mentors' => 'nullable|array',
            'mentors.*' => 'exists:mentors,id',
        ]);

        // Handle banner image
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('events', 'public');
        }

        // Update event
        $event->update($validated);

        // Sync mentors
        if ($request->has('mentors')) {
            $event->mentors()->sync($request->mentors ?? []);
        }

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Event updated successfully');
    }

    /**
     * Delete event
     */
    public function destroy(Event $event)
    {
        $title = $event->title;
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', "Event \"$title\" deleted successfully");
    }
}
