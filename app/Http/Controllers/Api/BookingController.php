<?php

namespace App\Http\Controllers\Api;

use App\Models\Inquiry;
use App\Models\Quote;
use App\Models\Booking;
use App\Http\Traits\ApiResponse;
use App\Notifications\BookingCreated;
use App\Notifications\BookingStatusUpdated;
use App\Notifications\ReviewRequest;
use App\Services\BookingInvoiceService;
use App\Services\NotificationService;
use App\Services\PhotographerNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    use ApiResponse;

    /**
     * Create inquiry
     */
    public function createInquiry(Request $request)
    {
        $validated = $request->validate([
            'photographer_id' => 'required|exists:photographers,id',
            'package_id' => 'nullable|exists:packages,id',
            'event_date' => 'required|date',
            'event_location' => 'required|string|max:255',
            'preferred_time_slot' => 'nullable|string|max:255',
            'event_type_detail' => 'nullable|string|max:255',
            'indoor_outdoor' => 'nullable|in:indoor,outdoor,both',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'guest_count' => 'required|integer|min:1',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'requirements' => 'nullable|string',
            'additional_services' => 'nullable|array',
            'additional_services.*' => 'string|max:100',
        ]);

        $authPhotographer = Auth::user()?->photographer;
        if ($authPhotographer && (int) $validated['photographer_id'] === (int) $authPhotographer->id) {
            return $this->validationError([
                'photographer_id' => ['You cannot create a booking request for your own profile.']
            ], 'Self booking is not allowed');
        }

        try {
            $result = DB::transaction(function () use ($validated) {
                $inquiry = Inquiry::create([
                    ...$validated,
                    'client_id' => Auth::id(),
                    'status' => 'new',
                    'expires_at' => now()->addDays(7),
                ]);

                // Create booking record
                $booking = Booking::create([
                    'inquiry_id' => $inquiry->id,
                    'client_id' => Auth::id(),
                    'photographer_id' => $validated['photographer_id'],
                    'package_id' => $validated['package_id'] ?? null,
                    'event_date' => $validated['event_date'],
                    'total_amount' => 0, // Will be updated when quote is accepted
                    'status' => 'pending_payment',
                    'payment_method' => 'manual',
                    'payment_gateway' => 'manual',
                ]);

                return ['inquiry' => $inquiry, 'booking' => $booking];
            });

            $inquiry = $result['inquiry'];
            $booking = $result['booking'];

            // Send notifications (outside transaction)
            $booking->load(['client', 'photographer.user']);
            
            // Notify photographer about new booking
            try {
                PhotographerNotificationService::notifyNewBooking($booking);
            } catch (\Exception $notificationError) {
                Log::warning('Failed to send photographer notification', [
                    'booking_id' => $booking->id,
                    'error' => $notificationError->getMessage(),
                ]);
            }

            // Track booking achievement
            \App\Services\AchievementService::trackBookingCreated(
                $validated['photographer_id'],
                $booking->total_amount
            );

            // Use NotificationService for automated notifications
            try {
                NotificationService::newBooking($booking);
            } catch (\Exception $notificationError) {
                // Log notification errors but don't fail the request
                Log::warning('Failed to send booking notifications', [
                    'booking_id' => $booking->id,
                    'error' => $notificationError->getMessage(),
                ]);
            }

            Log::info('Booking inquiry created successfully', [
                'inquiry_id' => $inquiry->id,
                'booking_id' => $booking->id,
                'client_id' => Auth::id(),
                'photographer_id' => $validated['photographer_id'],
            ]);

            return $this->created([
                'inquiry' => $inquiry,
                'booking' => $booking,
            ], 'Inquiry created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create booking inquiry', [
                'error' => $e->getMessage(),
                'client_id' => Auth::id(),
                'photographer_id' => $validated['photographer_id'] ?? null,
            ]);

            return $this->error('Failed to create inquiry. Please try again.', 500);
        }
    }

    /**
     * Get client bookings
     */
    public function myBookings(Request $request)
    {
        $bookings = Booking::where('client_id', Auth::id())
            ->with(['photographer', 'package', 'reviews'])
            ->orderBy('event_date', 'desc')
            ->paginate(20);

        return $this->paginated($bookings, 'Bookings retrieved successfully');
    }

    /**
     * Get booking details
     */
    public function getBooking(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load([
            'photographer',
            'package',
            'inquiry',
            'quote',
            'reviews',
        ]);

        return $this->success($booking, 'Booking retrieved successfully');
    }

    /**
     * Cancel booking
     */
    public function cancelBooking(Booking $booking, Request $request)
    {
        $this->authorize('update', $booking);

        if (!in_array($booking->status, ['pending_payment', 'confirmed'])) {
            return $this->error('Booking cannot be cancelled in current status', 422);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->get('reason'),
            'cancelled_at' => now(),
        ]);

        return $this->success($booking, 'Booking cancelled successfully');
    }

    /**
     * Update booking status (for photographers)
     */
    public function updateStatus(Booking $booking, Request $request)
    {
        // Check if user is the photographer for this booking
        $photographer = Auth::user()->photographer;
        if (!$photographer || $booking->photographer_id !== $photographer->id) {
            return $this->unauthorized('Unauthorized');
        }

        $validated = $request->validate([
            'status' => 'required|in:confirmed,rejected,completed,cancelled',
        ]);

        $oldStatus = $booking->status;

        $booking->update([
            'status' => $validated['status'],
        ]);

        // Notify photographer about status change
        try {
            PhotographerNotificationService::notifyBookingStatusChange($booking, $oldStatus, $validated['status']);
        } catch (\Exception $e) {
            Log::warning('Failed to send photographer notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Send notification to client about status change using NotificationService
        $booking->load(['client', 'photographer.user', 'package']);
        
        try {
            NotificationService::bookingStatusChanged($booking, $oldStatus, $validated['status']);
        } catch (\Exception $e) {
            Log::warning('Failed to send booking status notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        // If completed, send review request after 1 day
        if ($validated['status'] === 'completed') {
            try {
                if (class_exists(\App\Notifications\ReviewRequest::class)) {
                    $booking->client->notify((new ReviewRequest($booking))->delay(now()->addDay()));
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send review request', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->success($booking->fresh(['client', 'photographer', 'package']), 'Booking status updated successfully');
    }

    /**
     * Generate invoice for a booking
     */
    public function generateInvoice(Booking $booking)
    {
        // Authorization: client or photographer can request invoice
        $user = Auth::user();
        if ($booking->client_id !== $user->id && $booking->photographer_id !== $user->photographer?->id) {
            return $this->unauthorized('Unauthorized to generate invoice for this booking');
        }

        try {
            $invoiceService = new BookingInvoiceService();
            $filePath = $invoiceService->generateInvoice($booking);

            return $this->success([
                'file_path' => $filePath,
                'download_url' => route('bookings.invoice.download', $booking),
            ], 'Invoice generated successfully');
        } catch (\Exception $e) {
            Log::error('Invoice generation failed: ' . $e->getMessage());
            return $this->error('Failed to generate invoice: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Download invoice for a booking
     */
    public function downloadInvoice(Booking $booking)
    {
        // Authorization
        $user = Auth::user();
        if ($booking->client_id !== $user->id && $booking->photographer_id !== $user->photographer?->id) {
            return $this->unauthorized('Unauthorized to download invoice');
        }

        try {
            $invoiceService = new BookingInvoiceService();
            $filePath = $invoiceService->getInvoiceFilePath($booking);

            if (!$filePath || !Storage::exists($filePath)) {
                // Generate on-demand if not exists
                $filePath = $invoiceService->generateInvoice($booking);
            }

            return Storage::download($filePath, 'invoice_' . $booking->id . '.pdf');
        } catch (\Exception $e) {
            Log::error('Invoice download failed: ' . $e->getMessage());
            return $this->error('Failed to download invoice', 500);
        }
    }

    /**
     * Email invoice to booking parties
     */
    public function emailInvoice(Booking $booking)
    {
        // Authorization: only admin, client, or photographer
        $user = Auth::user();
        if (!$user->hasRole('admin') && $booking->client_id !== $user->id && $booking->photographer_id !== $user->photographer?->id) {
            return $this->unauthorized('Unauthorized');
        }

        try {
            $invoiceService = new BookingInvoiceService();
            $filePath = $invoiceService->getInvoiceFilePath($booking);

            if (!$filePath || !Storage::exists($filePath)) {
                $filePath = $invoiceService->generateInvoice($booking);
            }

            $invoiceService->emailInvoice($booking, $filePath);

            return $this->success([], 'Invoice emailed successfully to client and photographer');
        } catch (\Exception $e) {
            Log::error('Invoice email failed: ' . $e->getMessage());
            return $this->error('Failed to email invoice', 500);
        }
    }
}
