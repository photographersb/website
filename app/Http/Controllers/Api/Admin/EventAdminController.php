<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Event;
use App\Models\EventTicket;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class EventAdminController extends Controller
{
    /**
     * List all events for admin
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['category', 'city', 'organizer', 'creator'])
            ->orderBy('start_datetime', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->paginate($request->input('per_page', 15));

        return response()->json($events);
    }

    /**
     * Create event
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'city_id' => 'nullable|exists:locations,id',
            'venue' => 'nullable|string',
            'location_text' => 'nullable|string',
            'start_datetime' => 'required|date_format:Y-m-d H:i',
            'end_datetime' => 'required|date_format:Y-m-d H:i|after:start_datetime',
            'event_type' => 'required|in:free,paid',
            'base_price' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'booking_close_datetime' => 'nullable|date_format:Y-m-d H:i',
            'refund_policy' => 'nullable|string',
            'banner_image' => 'nullable|image',
        ]);

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('events', 'public');
        }

        $validated['slug'] = Event::generateSlug($validated['title']);
        $validated['created_by'] = Auth::id();
        $validated['organizer_id'] = $request->input('organizer_id', Auth::id());

        $event = Event::create($validated);

        return response()->json($event->load(['category', 'city', 'organizer']), 201);
    }

    /**
     * Update event
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'city_id' => 'nullable|exists:locations,id',
            'venue' => 'nullable|string',
            'location_text' => 'nullable|string',
            'start_datetime' => 'required|date_format:Y-m-d H:i',
            'end_datetime' => 'required|date_format:Y-m-d H:i|after:start_datetime',
            'event_type' => 'required|in:free,paid',
            'base_price' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'booking_close_datetime' => 'nullable|date_format:Y-m-d H:i',
            'refund_policy' => 'nullable|string',
            'banner_image' => 'nullable|image',
            'status' => 'required|in:draft,published,cancelled',
            'is_featured' => 'boolean',
        ]);

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('events', 'public');
        }

        $event->update($validated);

        return response()->json($event->load(['category', 'city', 'organizer']));
    }

    /**
     * Delete event
     */
    public function destroy(Event $event): JsonResponse
    {
        $event->delete();
        return response()->json(null, 204);
    }

    /**
     * Publish/Unpublish event
     */
    public function togglePublish(Event $event): JsonResponse
    {
        $newStatus = $event->status === 'published' ? 'draft' : 'published';
        $event->update(['status' => $newStatus]);

        return response()->json([
            'message' => "Event {$newStatus}",
            'event' => $event,
        ]);
    }

    /**
     * Toggle featured
     */
    public function toggleFeatured(Event $event): JsonResponse
    {
        $event->update(['is_featured' => !$event->is_featured]);

        return response()->json([
            'message' => $event->is_featured ? 'Event featured' : 'Event unfeatured',
            'event' => $event,
        ]);
    }

    /**
     * Manage ticket types
     */
    public function manageTickets(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'tickets' => 'required|array',
            'tickets.*.title' => 'required|string',
            'tickets.*.price' => 'required|numeric|min:0',
            'tickets.*.quantity' => 'required|integer|min:1',
            'tickets.*.sales_start_datetime' => 'required|date_format:Y-m-d H:i',
            'tickets.*.sales_end_datetime' => 'required|date_format:Y-m-d H:i',
        ]);

        $event->tickets()->delete();

        foreach ($validated['tickets'] as $ticketData) {
            $event->tickets()->create($ticketData);
        }

        return response()->json([
            'message' => 'Tickets updated',
            'tickets' => $event->tickets,
        ]);
    }

    /**
     * Export attendees list
     */
    public function exportAttendees(Event $event)
    {
        $registrations = $event->registrations()
            ->where('status', 'confirmed')
            ->orWhere('status', 'attended')
            ->with('user', 'ticket')
            ->get();

        $csv = "Name,Email,Phone,Ticket Type,Qty,Amount,Status,Checked In At\n";

        foreach ($registrations as $reg) {
            $csv .= implode(',', [
                $reg->user->name,
                $reg->user->email,
                $reg->user->phone ?? '',
                $reg->ticket?->title ?? 'N/A',
                $reg->qty,
                $reg->total_amount,
                $reg->status,
                $reg->attended_at ?? '',
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$event->slug}-attendees.csv");
    }

    /**
     * Get event payment report
     */
    public function paymentReport(Event $event): JsonResponse
    {
        $payments = $event->payments()
            ->where('status', 'completed')
            ->with('registration.user')
            ->paginate(20);

        return response()->json($payments);
    }

    /**
     * Get event check-in report
     */
    public function checkInReport(Event $event): JsonResponse
    {
        $checkins = $event->registrations()
            ->where('status', 'attended')
            ->with('user', 'checkedInBy')
            ->orderBy('attended_at', 'desc')
            ->paginate(20);

        return response()->json($checkins);
    }
}
