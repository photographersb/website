<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Event;
use App\Models\EventTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class AdminEventApiController extends Controller
{
    use ApiResponse;

    /**
     * Get all events (admin view)
     */
    public function index(Request $request)
    {
        try {
            $query = Event::with(['organizer', 'city']);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by city
            if ($request->has('city_id')) {
                $query->where('city_id', $request->city_id);
            }

            // Filter by event type
            if ($request->has('event_type')) {
                $query->where('event_type', $request->event_type);
            }

            // Filter by organizer
            if ($request->has('organizer_id')) {
                $query->where('organizer_id', $request->organizer_id);
            }

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortField = $request->get('sort_field', 'event_date');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // Get per_page from request, default to 20
            $perPage = $request->get('per_page', 20);
            $events = $query->paginate($perPage);

            return $this->success($events->items(), 'Events retrieved successfully', 200, [
                'total' => $events->total(),
                'per_page' => $events->perPage(),
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching events: ' . $e->getMessage(), ['exception' => $e]);
            return $this->error('Failed to fetch events: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get single event
     */
    public function show($id)
    {
        // Support both numeric ID and slug
        $event = is_numeric($id)
            ? Event::with([
                'organizer.user',
                'city',
                'category',
                'mentors',
                'sponsors',
                'certificateTemplate'
            ])->findOrFail($id)
            : Event::with([
                'organizer.user',
                'city',
                'category',
                'mentors',
                'sponsors',
                'certificateTemplate'
            ])->where('slug', $id)->firstOrFail();

        return $this->success($event, 'Event retrieved successfully');
    }

    /**
     * Create new event
     */
    public function store(EventStoreRequest $request)
    {
        $validated = $request->validated();

        // Generate slug
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $count = 1;

        while (Event::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $validated['slug'] = $slug;

        try {
            DB::beginTransaction();

            $nullableFields = [
                'certificate_template_id',
                'organizer_id',
                'created_by',
                'city_id',
                'capacity',
                'max_attendees',
                'max_tickets_per_user',
                'ticket_price',
                'price',
                'duration_hours',
                'latitude',
                'longitude',
            ];

            foreach ($nullableFields as $field) {
                if (array_key_exists($field, $validated) && $validated[$field] === '') {
                    $validated[$field] = null;
                }
            }
            
            // Auto-assign organizer_id to current admin if not provided
            if (empty($validated['organizer_id'])) {
                $currentUser = auth()->user();
                // If current user is a photographer, use their photographer record
                if ($currentUser->photographer) {
                    $validated['organizer_id'] = $currentUser->photographer->id;
                } else {
                    // Otherwise, find first active photographer or leave null
                    $photographer = \App\Models\Photographer::where('is_verified', true)->first();
                    if ($photographer) {
                        $validated['organizer_id'] = $photographer->id;
                    }
                    // If no verified photographer exists, organizer_id stays null
                }
            }

            // Set created_by to current admin if not provided
            if (empty($validated['created_by'])) {
                $validated['created_by'] = auth()->id();
            }

            // Extract mentor_ids / mentors if provided
            $mentorIds = $validated['mentor_ids'] ?? [];
            $mentors = $validated['mentors'] ?? [];
            $sponsors = $validated['sponsors'] ?? [];
            unset($validated['mentor_ids'], $validated['mentors'], $validated['sponsors']);
            
            $event = Event::create($validated);
            
            // Sync mentors if provided
            if (!empty($mentors)) {
                $syncData = [];
                foreach ($mentors as $index => $mentorRow) {
                    $mentorId = $mentorRow['mentor_id'] ?? null;
                    if (!$mentorId) {
                        continue;
                    }
                    $syncData[$mentorId] = [
                        'role' => $mentorRow['role'] ?? 'mentor',
                        'sort_order' => $mentorRow['sort_order'] ?? $index,
                    ];
                }
                $event->mentors()->sync($syncData);
            } elseif (!empty($mentorIds)) {
                $event->mentors()->sync($mentorIds);
            }

            if (!empty($sponsors)) {
                $syncSponsors = [];
                foreach ($sponsors as $index => $sponsorRow) {
                    $sponsorId = $sponsorRow['sponsor_id'] ?? null;
                    if (!$sponsorId) {
                        continue;
                    }
                    $syncSponsors[$sponsorId] = [
                        'tier' => $sponsorRow['tier'] ?? 'bronze',
                        'sort_order' => $sponsorRow['sort_order'] ?? $index,
                        'sponsored_amount' => $sponsorRow['sponsored_amount'] ?? null,
                    ];
                }
                $event->sponsors()->sync($syncSponsors);
            }

            $this->ensureDefaultTicket($event);
            
            DB::commit();

            Log::info('Event created successfully', [
                'event_id' => $event->id,
                'title' => $event->title,
                'organizer_id' => $validated['organizer_id'] ?? null,
                'mentors_count' => count($mentorIds),
                'admin_id' => auth()->id(),
            ]);

            return $this->created($event->load(['organizer.user', 'city', 'mentors', 'sponsors']), 'Event created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            $errorMessage = $e->getMessage();
            
            Log::error('Failed to create event', [
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString(),
                'title' => $validated['title'] ?? null,
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create event: ' . $errorMessage,
                'error' => config('app.debug') ? $errorMessage : null
            ], 500);
        }
    }

    /**
     * Update event
     */
    public function update(EventUpdateRequest $request, $id)
    {
        // Support both numeric ID and slug
        $event = is_numeric($id)
            ? Event::findOrFail($id)
            : Event::where('slug', $id)->firstOrFail();

        $validated = $request->validated();

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

        try {
            DB::beginTransaction();
            
            // Extract mentor_ids / mentors / sponsors if provided
            $mentorIds = $validated['mentor_ids'] ?? null;
            $mentors = $validated['mentors'] ?? null;
            $sponsors = $validated['sponsors'] ?? null;
            unset($validated['mentor_ids'], $validated['mentors'], $validated['sponsors']);
            
            $event->update($validated);
            
            // Sync mentors if provided (null means don't touch, empty array means clear all)
            if ($mentors !== null) {
                $syncData = [];
                foreach ($mentors as $index => $mentorRow) {
                    $mentorId = $mentorRow['mentor_id'] ?? null;
                    if (!$mentorId) {
                        continue;
                    }
                    $syncData[$mentorId] = [
                        'role' => $mentorRow['role'] ?? 'mentor',
                        'sort_order' => $mentorRow['sort_order'] ?? $index,
                    ];
                }
                $event->mentors()->sync($syncData);
            } elseif ($mentorIds !== null) {
                $event->mentors()->sync($mentorIds);
            }

            if ($sponsors !== null) {
                $syncSponsors = [];
                foreach ($sponsors as $index => $sponsorRow) {
                    $sponsorId = $sponsorRow['sponsor_id'] ?? null;
                    if (!$sponsorId) {
                        continue;
                    }
                    $syncSponsors[$sponsorId] = [
                        'tier' => $sponsorRow['tier'] ?? 'bronze',
                        'sort_order' => $sponsorRow['sort_order'] ?? $index,
                        'sponsored_amount' => $sponsorRow['sponsored_amount'] ?? null,
                    ];
                }
                $event->sponsors()->sync($syncSponsors);
            }

            $this->ensureDefaultTicket($event);
            
            DB::commit();

            Log::info('Event updated successfully', [
                'event_id' => $event->id,
                'title' => $event->title,
                'mentors_updated' => $mentorIds !== null,
                'admin_id' => auth()->id(),
            ]);
            
            // Clear events stats cache
            Cache::forget('events_stats');
            return $this->success($event->load(['organizer.user', 'city', 'mentors', 'sponsors']), 'Event updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update event', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
            ]);

            return $this->error('Failed to update event. Please try again.', 500);
        }
    }

    private function ensureDefaultTicket(Event $event): void
    {
        $price = $event->ticket_price ?? $event->price ?? $event->base_price ?? 0;
        $isPaid = ($event->event_mode === 'paid') || (bool) $event->is_ticketed || $price > 0;

        if (!$isPaid) {
            return;
        }

        if ($event->tickets()->exists()) {
            return;
        }

        if (!$price || $price <= 0) {
            return;
        }

        $quantity = $event->max_attendees ?? $event->capacity ?? 100;
        $salesStart = Carbon::now();
        $salesEnd = $event->registration_deadline ?? $event->event_date ?? Carbon::now()->addDays(30);

        if ($salesEnd instanceof \Illuminate\Support\Carbon) {
            $end = $salesEnd;
        } else {
            $end = Carbon::parse($salesEnd);
        }

        if ($end->lessThan($salesStart)) {
            $end = $salesStart->copy()->addDays(1);
        }

        $event->tickets()->create([
            'title' => 'General Admission',
            'price' => $price,
            'quantity' => $quantity,
            'sales_start_datetime' => $salesStart,
            'sales_end_datetime' => $end,
            'is_active' => true,
        ]);
    }

    /**
     * Delete event
     */
    public function destroy($id)
    {
        // Support both numeric ID and slug
        $event = is_numeric($id)
            ? Event::findOrFail($id)
            : Event::where('slug', $id)->firstOrFail();

        // Check if event has RSVPs
        $rsvpCount = $event->rsvp_count ?? 0;
        if ($rsvpCount > 0 && $event->status !== 'cancelled') {
            return $this->validationError(['event' => "Cannot delete event with {$rsvpCount} RSVPs. Please cancel the event first."], 'Validation failed');
        }

        try {
            DB::transaction(function () use ($event) {
                // Delete related RSVPs
                $event->rsvps()->delete();
                $event->delete();
            });

            Log::info('Event deleted successfully', [
                'event_id' => $id,
                'title' => $event->title,
                'admin_id' => auth()->id(),
            ]);

            return $this->success([], 'Event deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete event', [
                'event_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
            ]);

            return $this->error('Failed to delete event. Please try again.', 500);
        }
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'event_ids' => 'required|array|min:1|max:100',
            'event_ids.*' => 'required|integer|exists:events,id',
            'status' => 'required|in:draft,published,cancelled',
        ]);

        try {
            $updated = Event::whereIn('id', $validated['event_ids'])
                ->update(['status' => $validated['status']]);

            Log::info('Events bulk status updated', [
                'count' => $updated,
                'status' => $validated['status'],
                'admin_id' => auth()->id(),
            ]);

            return $this->success([], "Successfully updated {$updated} events");
        } catch (\Exception $e) {
            Log::error('Failed to bulk update events', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
            ]);

            return $this->error('Failed to update events. Please try again.', 500);
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        // Support both numeric ID and slug
        $event = is_numeric($id)
            ? Event::findOrFail($id)
            : Event::where('slug', $id)->firstOrFail();

        try {
            $event->update([
                'is_featured' => !$event->is_featured,
                'featured_until' => $event->is_featured ? null : now()->addDays(30),
            ]);

            Log::info('Event featured status toggled', [
                'event_id' => $event->id,
                'is_featured' => $event->is_featured,
                'admin_id' => auth()->id(),
            ]);

            return $this->success($event, $event->is_featured ? 'Event featured' : 'Event unfeatured');
        } catch (\Exception $e) {
            Log::error('Failed to toggle event featured status', [
                'event_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
            ]);

            return $this->error('Failed to update event. Please try again.', 500);
        }
    }
}
