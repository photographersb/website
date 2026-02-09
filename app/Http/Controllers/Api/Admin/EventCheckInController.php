<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventRsvp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EventCheckInController extends Controller
{
    private function useRegistrationTable(Event $event): bool
    {
        if (!Schema::hasTable('event_registrations')) {
            return false;
        }

        return EventRegistration::where('event_id', $event->id)->exists();
    }

    private function mapRsvp(EventRsvp $rsvp): array
    {
        return [
            'id' => $rsvp->id,
            'user' => $rsvp->user,
            'ticket' => null,
            'status' => $rsvp->check_in_at ? 'attended' : 'confirmed',
            'attended_at' => $rsvp->check_in_at,
        ];
    }

    /**
     * Get check-in page for event
     */
    public function index(Event $event): JsonResponse
    {
        if ($this->useRegistrationTable($event)) {
            $confirmed = EventRegistration::where('event_id', $event->id)
                ->where('status', 'confirmed')
                ->count();
            $attended = EventRegistration::where('event_id', $event->id)
                ->where('status', 'attended')
                ->count();

            $stats = [
                'total_confirmed' => $confirmed,
                'total_attended' => $attended,
                'pending_checkin' => max($confirmed - $attended, 0),
            ];
        } else {
            $confirmed = EventRsvp::where('event_id', $event->id)
                ->whereIn('rsvp_status', ['going', 'maybe'])
                ->count();
            $attended = EventRsvp::where('event_id', $event->id)
                ->whereNotNull('check_in_at')
                ->count();

            $stats = [
                'total_confirmed' => $confirmed,
                'total_attended' => $attended,
                'pending_checkin' => max($confirmed - $attended, 0),
            ];
        }

        return response()->json([
            'event' => $event->load(['city', 'organizer']),
            'stats' => $stats,
        ]);
    }

    /**
     * Scan QR code and check in
     */
    public function scan(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'qr_token' => 'required|string',
        ]);

        if (!$this->useRegistrationTable($event)) {
            return response()->json([
                'success' => false,
                'message' => 'QR check-in is not available for RSVP registrations.',
            ], 400);
        }

        try {
            return DB::transaction(function () use ($event, $validated) {
                $registration = EventRegistration::where('event_id', $event->id)
                    ->where('qr_token', $validated['qr_token'])
                    ->first();

                if (!$registration) {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR code not found or invalid',
                    ], 404);
                }

                if ($registration->isAttended()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This ticket has already been checked in at ' . $registration->attended_at->format('H:i:s'),
                    ], 400);
                }

                if (!$registration->canCheckIn()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Registration status: ' . $registration->status,
                    ], 400);
                }

                $registration->markAsAttended(Auth::id());

                return response()->json([
                    'success' => true,
                    'message' => 'Check-in successful',
                    'registration' => $registration->load('user', 'event', 'ticket'),
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing check-in: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of registrations for check-in
     */
    public function getRegistrations(Event $event, Request $request): JsonResponse
    {
        if ($this->useRegistrationTable($event)) {
            $query = EventRegistration::where('event_id', $event->id)
                ->where('status', 'confirmed')
                ->with('user', 'ticket')
                ->orderBy('created_at', 'asc');
        } else {
            $query = EventRsvp::where('event_id', $event->id)
                ->whereIn('rsvp_status', ['going', 'maybe'])
                ->with('user')
                ->orderBy('created_at', 'asc');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $registrations = $query->paginate(50);

        if ($this->useRegistrationTable($event)) {
            return response()->json($registrations);
        }

        $mapped = $registrations->getCollection()->map(function ($rsvp) {
            return $this->mapRsvp($rsvp);
        });

        return response()->json([
            'data' => $mapped,
            'current_page' => $registrations->currentPage(),
            'last_page' => $registrations->lastPage(),
            'total' => $registrations->total(),
        ]);
    }

    /**
     * Get registration by QR token
     */
    public function getByQrToken(Event $event, $qrToken): JsonResponse
    {
        if (!$this->useRegistrationTable($event)) {
            return response()->json(['error' => 'QR check-in is not available for RSVP registrations'], 400);
        }

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('qr_token', $qrToken)
            ->with('user', 'ticket')
            ->first();

        if (!$registration) {
            return response()->json(['error' => 'Registration not found'], 404);
        }

        return response()->json($registration);
    }

    /**
     * Mark manual check-in (without QR)
     */
    public function manualCheckIn(Request $request, Event $event): JsonResponse
    {
        $table = $this->useRegistrationTable($event) ? 'event_registrations' : 'event_rsvps';

        $validated = $request->validate([
            'registration_id' => 'required|exists:' . $table . ',id',
        ]);

        try {
            return DB::transaction(function () use ($event, $validated) {
                if ($this->useRegistrationTable($event)) {
                    $registration = EventRegistration::findOrFail($validated['registration_id']);

                    if ($registration->event_id !== $event->id) {
                        return response()->json(['error' => 'Registration not found for this event'], 400);
                    }

                    if ($registration->isAttended()) {
                        return response()->json([
                            'error' => 'Already checked in at ' . $registration->attended_at->format('H:i:s'),
                        ], 400);
                    }

                    $registration->markAsAttended(Auth::id());

                    return response()->json([
                        'message' => 'Check-in successful',
                        'registration' => $registration->load('user', 'ticket'),
                    ]);
                }

                $registration = EventRsvp::findOrFail($validated['registration_id']);

                if ($registration->event_id !== $event->id) {
                    return response()->json(['error' => 'Registration not found for this event'], 400);
                }

                if ($registration->check_in_at) {
                    return response()->json([
                        'error' => 'Already checked in at ' . $registration->check_in_at->format('H:i:s'),
                    ], 400);
                }

                $registration->update([
                    'check_in_at' => now(),
                ]);

                return response()->json([
                    'message' => 'Check-in successful',
                    'registration' => $this->mapRsvp($registration->load('user')),
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Undo check-in
     */
    public function undoCheckIn($registration): JsonResponse
    {
        if (Schema::hasTable('event_registrations')) {
            $record = EventRegistration::find($registration);
            if ($record) {
                if (!$record->isAttended()) {
                    return response()->json(['error' => 'Registration not checked in'], 400);
                }

                $record->update([
                    'status' => 'confirmed',
                    'attended_at' => null,
                    'checked_in_by' => null,
                ]);

                return response()->json([
                    'message' => 'Check-in undone',
                    'registration' => $record,
                ]);
            }
        }

        $record = EventRsvp::find($registration);
        if (!$record) {
            return response()->json(['error' => 'Registration not found'], 404);
        }

        if (!$record->check_in_at) {
            return response()->json(['error' => 'Registration not checked in'], 400);
        }

        $record->update([
            'check_in_at' => null,
        ]);

        return response()->json([
            'message' => 'Check-in undone',
            'registration' => $this->mapRsvp($record->load('user')),
        ]);
    }

    /**
     * Export check-in report
     */
    public function exportCheckInReport(Event $event)
    {
        if ($this->useRegistrationTable($event)) {
            $registrations = EventRegistration::where('event_id', $event->id)
                ->where('status', 'attended')
                ->with('user', 'checkedInBy', 'ticket')
                ->orderBy('attended_at', 'desc')
                ->get();
        } else {
            $registrations = EventRsvp::where('event_id', $event->id)
                ->whereNotNull('check_in_at')
                ->with('user')
                ->orderBy('check_in_at', 'desc')
                ->get();
        }

        $csv = "Name,Email,Ticket,Check-in Time,Checked In By\n";

        foreach ($registrations as $reg) {
            $checkedAt = $reg->attended_at ?? $reg->check_in_at;
            $csv .= implode(',', [
                $reg->user->name,
                $reg->user->email,
                $reg->ticket?->title ?? 'N/A',
                $checkedAt ? $checkedAt->format('Y-m-d H:i:s') : 'N/A',
                $reg->checkedInBy?->name ?? 'Unknown',
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$event->slug}-checkin-report.csv");
    }
}<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventRsvp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EventCheckInController extends Controller
{
    private function useRegistrationTable(Event $event): bool
    {
        if (!Schema::hasTable('event_registrations')) {
            return false;
        }

        return EventRegistration::where('event_id', $event->id)->exists();
    }

    private function mapRsvp(EventRsvp $rsvp): array
    {
        return [
            'id' => $rsvp->id,
            'user' => $rsvp->user,
            'ticket' => null,
            'status' => $rsvp->check_in_at ? 'attended' : 'confirmed',
            'attended_at' => $rsvp->check_in_at,
        ];
    }

    /**
     * Get check-in page for event
     */
    public function index(Event $event): JsonResponse
    {
        if ($this->useRegistrationTable($event)) {
            $confirmed = EventRegistration::where('event_id', $event->id)
                ->where('status', 'confirmed')
                ->count();
            $attended = EventRegistration::where('event_id', $event->id)
                ->where('status', 'attended')
                ->count();

            $stats = [
                'total_confirmed' => $confirmed,
                'total_attended' => $attended,
                'pending_checkin' => max($confirmed - $attended, 0),
            ];
        } else {
            $confirmed = EventRsvp::where('event_id', $event->id)
                ->whereIn('rsvp_status', ['going', 'maybe'])
                ->count();
            $attended = EventRsvp::where('event_id', $event->id)
                ->whereNotNull('check_in_at')
                ->count();

            $stats = [
                'total_confirmed' => $confirmed,
                'total_attended' => $attended,
                'pending_checkin' => max($confirmed - $attended, 0),
            ];
        }

        return response()->json([
            'event' => $event->load(['city', 'organizer']),
            'stats' => $stats,
        ]);
    }

    /**
     * Scan QR code and check in
     */
    public function scan(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'qr_token' => 'required|string',
        ]);

        if (!$this->useRegistrationTable($event)) {
            return response()->json([
                'success' => false,
                'message' => 'QR check-in is not available for RSVP registrations.',
            ], 400);
        }

        try {
            return DB::transaction(function () use ($event, $validated) {
                $registration = EventRegistration::where('event_id', $event->id)
                    ->where('qr_token', $validated['qr_token'])
                    ->first();

                if (!$registration) {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR code not found or invalid',
                    ], 404);
                }

                if ($registration->isAttended()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This ticket has already been checked in at ' . $registration->attended_at->format('H:i:s'),
                    ], 400);
                }

                if (!$registration->canCheckIn()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Registration status: ' . $registration->status,
                    ], 400);
                }

                $registration->markAsAttended(Auth::id());

                return response()->json([
                    'success' => true,
                    'message' => 'Check-in successful',
                    'registration' => $registration->load('user', 'event', 'ticket'),
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing check-in: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of registrations for check-in
     */
    public function getRegistrations(Event $event, Request $request): JsonResponse
    {
        if ($this->useRegistrationTable($event)) {
            $query = EventRegistration::where('event_id', $event->id)
                ->where('status', 'confirmed')
                ->with('user', 'ticket')
                ->orderBy('created_at', 'asc');
        } else {
            $query = EventRsvp::where('event_id', $event->id)
                ->whereIn('rsvp_status', ['going', 'maybe'])
                ->with('user')
                ->orderBy('created_at', 'asc');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $registrations = $query->paginate(50);

        if ($this->useRegistrationTable($event)) {
            return response()->json($registrations);
        }

        $mapped = $registrations->getCollection()->map(function ($rsvp) {
            return $this->mapRsvp($rsvp);
        });

        return response()->json([
            'data' => $mapped,
            'current_page' => $registrations->currentPage(),
            'last_page' => $registrations->lastPage(),
            'total' => $registrations->total(),
        ]);
    }

    /**
     * Get registration by QR token
     */
    public function getByQrToken(Event $event, $qrToken): JsonResponse
    {
        if (!$this->useRegistrationTable($event)) {
            return response()->json(['error' => 'QR check-in is not available for RSVP registrations'], 400);
        }

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('qr_token', $qrToken)
            ->with('user', 'ticket')
            ->first();

        if (!$registration) {
            return response()->json(['error' => 'Registration not found'], 404);
        }

        return response()->json($registration);
    }

    /**
     * Mark manual check-in (without QR)
     */
    public function manualCheckIn(Request $request, Event $event): JsonResponse
    {
        $table = $this->useRegistrationTable($event) ? 'event_registrations' : 'event_rsvps';

        $validated = $request->validate([
            'registration_id' => 'required|exists:' . $table . ',id',
        ]);

        try {
            return DB::transaction(function () use ($event, $validated) {
                if ($this->useRegistrationTable($event)) {
                    $registration = EventRegistration::findOrFail($validated['registration_id']);

                    if ($registration->event_id !== $event->id) {
                        return response()->json(['error' => 'Registration not found for this event'], 400);
                    }

                    if ($registration->isAttended()) {
                        return response()->json([
                            'error' => 'Already checked in at ' . $registration->attended_at->format('H:i:s'),
                        ], 400);
                    }

                    $registration->markAsAttended(Auth::id());

                    return response()->json([
                        'message' => 'Check-in successful',
                        'registration' => $registration->load('user', 'ticket'),
                    ]);
                }

                $registration = EventRsvp::findOrFail($validated['registration_id']);

                if ($registration->event_id !== $event->id) {
                    return response()->json(['error' => 'Registration not found for this event'], 400);
                }

                if ($registration->check_in_at) {
                    return response()->json([
                        'error' => 'Already checked in at ' . $registration->check_in_at->format('H:i:s'),
                    ], 400);
                }

                $registration->update([
                    'check_in_at' => now(),
                ]);

                return response()->json([
                    'message' => 'Check-in successful',
                    'registration' => $this->mapRsvp($registration->load('user')),
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Undo check-in
     */
    public function undoCheckIn($registration): JsonResponse
    {
        if (Schema::hasTable('event_registrations')) {
            $record = EventRegistration::find($registration);
            if ($record) {
                if (!$record->isAttended()) {
                    return response()->json(['error' => 'Registration not checked in'], 400);
                }

                $record->update([
                    'status' => 'confirmed',
                    'attended_at' => null,
                    'checked_in_by' => null,
                ]);

                return response()->json([
                    'message' => 'Check-in undone',
                    'registration' => $record,
                ]);
            }
        }

        $record = EventRsvp::find($registration);
        if (!$record) {
            return response()->json(['error' => 'Registration not found'], 404);
        }

        if (!$record->check_in_at) {
            return response()->json(['error' => 'Registration not checked in'], 400);
        }

        $record->update([
            'check_in_at' => null,
        ]);

        return response()->json([
            'message' => 'Check-in undone',
            'registration' => $this->mapRsvp($record->load('user')),
        ]);
    }

    /**
     * Export check-in report
     */
    public function exportCheckInReport(Event $event)
    {
        if ($this->useRegistrationTable($event)) {
            $registrations = EventRegistration::where('event_id', $event->id)
                ->where('status', 'attended')
                ->with('user', 'checkedInBy', 'ticket')
                ->orderBy('attended_at', 'desc')
                ->get();
        } else {
            $registrations = EventRsvp::where('event_id', $event->id)
                ->whereNotNull('check_in_at')
                ->with('user')
                ->orderBy('check_in_at', 'desc')
                ->get();
        }

        $csv = "Name,Email,Ticket,Check-in Time,Checked In By\n";

        foreach ($registrations as $reg) {
            $checkedAt = $reg->attended_at ?? $reg->check_in_at;
            $csv .= implode(',', [
                $reg->user->name,
                $reg->user->email,
                $reg->ticket?->title ?? 'N/A',
                $checkedAt ? $checkedAt->format('Y-m-d H:i:s') : 'N/A',
                $reg->checkedInBy?->name ?? 'Unknown',
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$event->slug}-checkin-report.csv");
    }
}

        return response()->json([
            'message' => 'Check-in undone',
            'registration' => $this->mapRsvp($record->load('user')),
        ]);
        }
    }

    /**
        if ($this->useRegistrationTable($event)) {
            $registrations = EventRegistration::where('event_id', $event->id)
                ->where('status', 'attended')
                ->with('user', 'checkedInBy', 'ticket')
                ->orderBy('attended_at', 'desc')
                ->get();
        } else {
            $registrations = EventRsvp::where('event_id', $event->id)
                ->whereNotNull('check_in_at')
                ->with('user')
                ->orderBy('check_in_at', 'desc')
                ->get();
        }
            return response()->json(['error' => 'Registration not checked in'], 400);
        }

        $registration->update([
            $checkedAt = $reg->attended_at ?? $reg->check_in_at;
            $csv .= implode(',', [
                $reg->user->name,
                $reg->user->email,
                $reg->ticket?->title ?? 'N/A',
                $checkedAt ? $checkedAt->format('Y-m-d H:i:s') : 'N/A',
                $reg->checkedInBy?->name ?? 'Unknown',
            ]) . "\n";
            'registration' => $registration,
        ]);
    }

    /**
     * Export check-in report
     */
    public function exportCheckInReport(Event $event)
    {
        $registrations = $event->registrations()
            ->where('status', 'attended')
            ->with('user', 'checkedInBy', 'ticket')
            ->orderBy('attended_at', 'desc')
            ->get();

        $csv = "Name,Email,Ticket,Check-in Time,Checked In By\n";

        foreach ($registrations as $reg) {
            $csv .= implode(',', [
                $reg->user->name,
                $reg->user->email,
                $reg->ticket?->title ?? 'N/A',
                $reg->attended_at->format('Y-m-d H:i:s'),
                $reg->checkedInBy?->name ?? 'Unknown',
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$event->slug}-checkin-report.csv");
    }
}
