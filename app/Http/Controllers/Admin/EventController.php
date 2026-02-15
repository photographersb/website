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
            'event' => new Event(),
            'cities' => Location::where('is_active', true)
                ->whereIn('type', ['district', 'upazila'])
                ->orderBy('name')
                ->get(),
            'categories' => Category::orderBy('name')->get(),
            'mentors' => Mentor::orderBy('name')->get(),
            'certificateTemplates' => CertificateTemplate::orderBy('title')->get(),
        ]);
    }

    /**
     * Store new event
     */
    public function store(Request $request)
    {
        $isDraft = $request->input('status') === 'draft';
        
        // Build validation rules based on draft status
        $rules = [
            'title' => 'required|string|max:255',
            'description' => $isDraft ? 'nullable|string' : 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'organizer_id' => 'nullable|exists:photographers,id',
            'city_id' => $isDraft ? 'nullable|exists:locations,id' : 'required|exists:locations,id',
            'venue_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'venue_address' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'google_map_link' => 'nullable|url',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'event_date' => $isDraft ? 'nullable|date_format:Y-m-d' : 'required|date_format:Y-m-d',
            'event_end_date' => 'nullable|date_format:Y-m-d',
            'start_time' => $isDraft ? 'nullable|date_format:H:i' : 'required|date_format:H:i',
            'end_time' => $isDraft ? 'nullable|date_format:H:i' : 'required|date_format:H:i',
            'all_day_event' => 'boolean',
            'duration_hours' => 'nullable|numeric|min:0.5',
            'max_attendees' => 'nullable|integer|min:1',
            'capacity' => $isDraft ? 'nullable|integer|min:1' : 'required|integer|min:1',
            'registration_deadline' => 'nullable|date_format:Y-m-d H:i',
            'event_type' => $isDraft ? 'nullable|in:free,paid' : 'required|in:free,paid',
            'price' => 'nullable|numeric|min:0',
            'ticket_price' => 'nullable|numeric|min:0',
            'is_ticketed' => 'boolean',
            'require_registration' => 'boolean',
            'certificates_enabled' => 'boolean',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
            'refund_policy' => 'nullable|string',
            'requirements' => 'nullable|string',
            'banner_image' => 'nullable|image|max:5120',
            'hero_image_url' => 'nullable|string',
            'hero_image_credit_name' => 'nullable|string',
            'hero_image_credit_url' => 'nullable|string',
            'banner_image_credit_name' => 'nullable|string',
            'banner_image_credit_url' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'status' => 'in:draft,published,cancelled',
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date_format:Y-m-d H:i',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string',
            'mentor_ids' => 'nullable|array',
            'mentor_ids.*' => 'exists:mentors,id',
            'sponsor_ids' => 'nullable|array',
            'sponsor_ids.*' => 'exists:sponsors,id',
        ];
        
        $validated = $request->validate($rules);

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
        if ($request->has('mentor_ids') && is_array($request->mentor_ids) && count($request->mentor_ids) > 0) {
            $event->mentors()->sync($request->mentor_ids);
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
            'certificateTemplates' => CertificateTemplate::orderBy('title')->get(),
        ]);
    }

    /**
     * Update event
     */
    public function update(Request $request, Event $event)
    {
        $isDraft = $request->input('status') === 'draft';
        
        // Build validation rules based on draft status
        $rules = [
            'title' => 'required|string|max:255',
            'description' => $isDraft ? 'nullable|string' : 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'organizer_id' => 'nullable|exists:photographers,id',
            'city_id' => $isDraft ? 'nullable|exists:locations,id' : 'required|exists:locations,id',
            'venue_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'venue_address' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'google_map_link' => 'nullable|url',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'event_date' => $isDraft ? 'nullable|date_format:Y-m-d' : 'required|date_format:Y-m-d',
            'event_end_date' => 'nullable|date_format:Y-m-d',
            'start_time' => $isDraft ? 'nullable|date_format:H:i' : 'required|date_format:H:i',
            'end_time' => $isDraft ? 'nullable|date_format:H:i' : 'required|date_format:H:i',
            'all_day_event' => 'boolean',
            'duration_hours' => 'nullable|numeric|min:0.5',
            'max_attendees' => 'nullable|integer|min:1',
            'capacity' => $isDraft ? 'nullable|integer|min:1' : 'required|integer|min:1',
            'registration_deadline' => 'nullable|date_format:Y-m-d H:i',
            'event_type' => $isDraft ? 'nullable|in:free,paid' : 'required|in:free,paid',
            'price' => 'nullable|numeric|min:0',
            'ticket_price' => 'nullable|numeric|min:0',
            'is_ticketed' => 'boolean',
            'require_registration' => 'boolean',
            'certificates_enabled' => 'boolean',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
            'refund_policy' => 'nullable|string',
            'requirements' => 'nullable|string',
            'banner_image' => 'nullable|image|max:5120',
            'hero_image_url' => 'nullable|string',
            'hero_image_credit_name' => 'nullable|string',
            'hero_image_credit_url' => 'nullable|string',
            'banner_image_credit_name' => 'nullable|string',
            'banner_image_credit_url' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'status' => 'in:draft,published,cancelled',
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date_format:Y-m-d H:i',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string',
            'mentor_ids' => 'nullable|array',
            'mentor_ids.*' => 'exists:mentors,id',
            'sponsor_ids' => 'nullable|array',
            'sponsor_ids.*' => 'exists:sponsors,id',
        ];
        
        $validated = $request->validate($rules);

        // Handle banner image
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('events', 'public');
        }

        // Update event
        $event->update($validated);

        // Sync mentors
        if ($request->has('mentor_ids')) {
            $event->mentors()->sync($request->mentor_ids ?? []);
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
