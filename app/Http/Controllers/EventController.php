<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Show event detail page (public)
     */
    public function show(Event $event)
    {
        // Only show published events
        if ($event->status !== 'published') {
            abort(404);
        }

        $event->load(['city', 'mentors', 'category', 'organizer']);

        // Calculate stats
        $registeredCount = $event->registrations()->count();
        $capacityFull = $event->capacity && $registeredCount >= $event->capacity;
        $capacityPercent = $event->capacity ? min(round(($registeredCount / $event->capacity) * 100), 100) : 0;
        
        // Check registration deadline
        $registrationClosed = false;
        $regDaysLeft = null;
        if ($event->registration_deadline) {
            $registrationClosed = now() > $event->registration_deadline;
            $regDaysLeft = now()->diffInDays($event->registration_deadline);
        }

        // Check if user is already registered
        $alreadyRegistered = false;
        $userRegistration = null;
        if (Auth::check()) {
            $userRegistration = $event->registrations()
                ->where('user_id', Auth::id())
                ->first();
            $alreadyRegistered = $userRegistration ? true : false;
        }

        return view('events.show', [
            'event' => $event,
            'registeredCount' => $registeredCount,
            'capacityFull' => $capacityFull,
            'capacityPercent' => $capacityPercent,
            'registrationClosed' => $registrationClosed,
            'regDaysLeft' => $regDaysLeft,
            'alreadyRegistered' => $alreadyRegistered,
            'userRegistration' => $userRegistration,
        ]);
    }

    /**
     * Register for an event
     */
    public function register(Request $request, Event $event)
    {
        // Must be authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('return_to', route('events.show', $event));
        }

        // Event must be published
        if ($event->status !== 'published') {
            return redirect()->route('events.show', $event)
                ->with('error', 'This event is not available for registration.');
        }

        // Check if already registered
        $existing = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return redirect()->route('events.show', $event)
                ->with('info', 'You are already registered for this event.');
        }

        // Check capacity
        if ($event->capacity) {
            $registered = $event->registrations()->count();
            if ($registered >= $event->capacity) {
                return redirect()->route('events.show', $event)
                    ->with('error', 'This event has reached maximum capacity.');
            }
        }

        // Check registration deadline
        if ($event->registration_deadline && now() > $event->registration_deadline) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Registration deadline has passed.');
        }

        // Determine payment status
        $paymentStatus = $event->event_type === 'free' ? 'free' : 'unpaid';

        // Generate registration code
        $registrationCode = 'REG-' . strtoupper(Str::random(8));

        // Create registration
        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'registration_code' => $registrationCode,
            'payment_status' => $paymentStatus,
            'registered_at' => now(),
        ]);

        // For free events, auto-confirm
        if ($event->event_type === 'free') {
            return redirect()->route('registrations.confirmation', $registration)
                ->with('success', 'Successfully registered! Your registration code is: ' . $registrationCode);
        }

        // For paid events, redirect to payment
        return redirect()->route('registrations.payment', $registration)
            ->with('success', 'Registration created. Please proceed with payment.');
    }

    /**
     * Show payment page (for paid events)
     */
    public function payment(EventRegistration $registration)
    {
        // User can only see their own payments
        if ($registration->user_id !== auth()->id()) {
            abort(403);
        }

        $registration->load(['event']);

        return view('events.payment', [
            'registration' => $registration,
            'event' => $registration->event,
        ]);
    }

    /**
     * Process payment callback
     */
    public function paymentCallback(Request $request, EventRegistration $registration)
    {
        // User can only process their own payments
        if ($registration->user_id !== auth()->id()) {
            abort(403);
        }

        // For demo: redirect to confirmation
        // In production: verify with payment gateway
        $provider = $request->input('provider', 'stripe');
        $paymentMethod = $request->input('payment_method');

        // Validate payment
        $validated = $request->validate([
            'provider' => 'required|in:stripe,sslcommerz',
            'payment_method' => 'nullable|in:bkash,nagad,rocket',
        ]);

        // Mark as paid
        $registration->update(['payment_status' => 'paid']);

        return redirect()->route('registrations.confirmation', $registration)
            ->with('success', 'Payment successful! Your ticket QR code is ready.');
    }

    /**
     * Show confirmation page with QR code
     */
    public function confirmation(EventRegistration $registration)
    {
        // User can only see their own confirmation
        if ($registration->user_id !== auth()->id()) {
            abort(403);
        }

        $registration->load(['event']);

        // Generate QR code if not exists
        if (!$registration->ticket_qr_path) {
            // QR code generation would happen here
            // For now, we'll use a placeholder
        }

        return view('events.confirmation', [
            'registration' => $registration,
            'event' => $registration->event,
        ]);
    }

    /**
     * Download ticket/QR code
     */
    public function downloadTicket(EventRegistration $registration)
    {
        // User can only download their own
        if ($registration->user_id !== auth()->id()) {
            abort(403);
        }

        // Generate PDF with QR code
        // This would use a PDF library
        // For now, return success message
        
        return response()->json([
            'message' => 'Ticket generated',
            'url' => $registration->ticket_qr_path
        ]);
    }
}
