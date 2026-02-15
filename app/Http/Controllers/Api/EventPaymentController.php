<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;

use App\Models\Event;
use App\Models\EventTicket;
use App\Models\EventRegistration;
use App\Models\EventPayment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\BookingStatusValidator;
use App\Services\EventCapacityLockService;
use App\Services\PaymentVerificationService;
use App\Http\Traits\ApiResponse;

class EventPaymentController extends Controller
{
    use ApiResponse;
    /**
     * Initiate payment for event ticket
     */
    public function initiate(Request $request, Event $event): JsonResponse
    {
        if (!Auth::check()) {
            return $this->unauthorized('Unauthorized');
        }

        if ($event->status !== 'published') {
            return $this->error('Event is not available for purchase', 400);
        }

        if ($event->registration_deadline && now()->greaterThan($event->registration_deadline)) {
            return $this->error('Registration deadline has passed', 400);
        }

        try {
            $validated = $request->validate([
                'ticket_id' => 'required|exists:event_tickets,id',
                'qty' => 'required|integer|min:1|max:100',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Payment initiation validation failed', [
                'errors' => $e->errors(),
                'payload' => $request->all(),
            ]);
            return $this->error('Invalid request: ' . implode(', ', array_map(fn($msgs) => implode(', ', $msgs), $e->errors())), 422);
        }

        $ticket = EventTicket::findOrFail($validated['ticket_id']);

        // Verify ticket belongs to event
        if ($ticket->event_id !== $event->id) {
            return $this->error('Ticket not found for this event', 400);
        }

        if (!$ticket->is_active) {
            return $this->error('Ticket sales are not active', 400);
        }

        if (!$ticket->isOnSale()) {
            return $this->error('Ticket sales are closed', 400);
        }

        $maxPerUser = $event->max_tickets_per_user;
        if ($maxPerUser && $validated['qty'] > $maxPerUser) {
            return $this->error('Maximum tickets per user exceeded', 400);
        }

        // Check availability
        if ($ticket->getAvailableQuantity() < $validated['qty']) {
            return $this->error('Not enough tickets available', 400);
        }

        if ($validated['qty'] <= 0) {
            return $this->error('Invalid ticket quantity', 400);
        }

        // Check if already registered
        $existing = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('ticket_id', $ticket->id)
            ->whereIn('status', ['confirmed', 'pending_payment'])
            ->first();

        if ($existing && $existing->status === 'confirmed') {
            return $this->error('You already have a confirmed registration for this ticket type', 400);
        }

        // If pending_payment exists but is old (>1 hour), allow new attempt (old attempt likely failed/abandoned)
        if ($existing && $existing->status === 'pending_payment') {
            $createdAt = $existing->created_at;
            $isOldAttempt = $createdAt && now()->diffInMinutes($createdAt) > 60;
            
            if (!$isOldAttempt) {
                return $this->error('You have a pending payment. Please complete it or wait before trying again.', 400);
            }
            
            // Delete old pending registration and payment to allow retry
            EventCapacityLockService::releaseReservation($existing);
            $existing->payment?->delete();
            $existing->delete();
        }

        $totalAmount = $ticket->price * $validated['qty'];

        try {
            return DB::transaction(function () use ($event, $ticket, $validated, $totalAmount) {
                $reserveResult = EventCapacityLockService::reserve(
                    $event,
                    $ticket,
                    (int) $validated['qty'],
                    (int) Auth::id()
                );

                if (!$reserveResult['success']) {
                    throw new \Exception($reserveResult['error'] ?? 'Capacity reservation failed');
                }

                $lockToken = $reserveResult['data']['lock_token'];
                $expiresAt = $reserveResult['data']['expires_at'];

                // Create registration with pending_payment status
                $registration = EventRegistration::create([
                    'event_id' => $event->id,
                    'user_id' => Auth::id(),
                    'ticket_id' => $ticket->id,
                    'qty' => $validated['qty'],
                    'total_amount' => $totalAmount,
                    'status' => 'pending_payment',
                    'lock_token' => $lockToken,
                    'locked_at' => now(),
                    'payment_expires_at' => $expiresAt,
                ]);

                // Create payment record
                $payment = EventPayment::create([
                    'event_id' => $event->id,
                    'registration_id' => $registration->id,
                    'user_id' => Auth::id(),
                    'event_registration_id' => $registration->id,
                    'gateway' => 'sslcommerz',
                    'method' => 'card',
                    'transaction_id' => 'pending-' . Str::random(20),
                    'amount' => $totalAmount,
                    'currency' => 'BDT',
                    'status' => 'pending',
                ]);

                $paymentGatewayData = $this->initiateSslcommerz(
                    $payment,
                    $registration,
                    $totalAmount
                );

                if (!empty($paymentGatewayData['error'])) {
                    // Update payment status to reflect the error
                    $payment->update(['status' => 'failed']);
                    // Throw exception to rollback transaction
                    throw new \Exception($paymentGatewayData['error']);
                }

                return $this->created([
                    'registration_id' => $registration->id,
                    'payment_gateway' => $paymentGatewayData,
                ], 'Payment initiated');
            });
        } catch (\Exception $e) {
            \Log::error('Payment initiation failed', ['error' => $e->getMessage()]);
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Submit manual payment proof for event ticket
     */
    public function manual(Request $request, Event $event): JsonResponse
    {
        if (!Auth::check()) {
            return $this->unauthorized('Unauthorized');
        }

        if ($event->status !== 'published') {
            return $this->error('Event is not available for purchase', 400);
        }

        if ($event->registration_deadline && now()->greaterThan($event->registration_deadline)) {
            return $this->error('Registration deadline has passed', 400);
        }

        try {
            $validated = $request->validate([
                'ticket_id' => 'required|exists:event_tickets,id',
                'qty' => 'required|integer|min:1|max:100',
                'method' => 'required|in:bkash,nagad,rocket,manual',
                'sender_number' => 'required|string|max:30',
                'trx_id' => 'nullable|string|max:100',
                'screenshot' => 'nullable|image|max:5120',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Manual payment validation failed', [
                'errors' => $e->errors(),
                'payload' => $request->except('screenshot'),
            ]);
            return $this->error('Validation failed: ' . implode(', ', array_map(fn($msgs) => implode(', ', $msgs), $e->errors())), 422);
        }

        \Log::info('Manual payment request received', [
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'method' => $validated['method'] ?? 'unknown',
            'ticket_id' => $validated['ticket_id'] ?? 'unknown',
            'qty' => $validated['qty'] ?? 'unknown',
        ]);

        if (in_array($validated['method'], ['bkash', 'nagad', 'rocket'], true)) {
            if (empty($validated['trx_id'])) {
                return $this->error('Transaction ID is required for this payment method', 422);
            }
            if (!$request->hasFile('screenshot')) {
                return $this->error('Payment screenshot is required for this payment method', 422);
            }
        }

        $ticket = EventTicket::findOrFail($validated['ticket_id']);

        // Verify ticket belongs to event
        if ($ticket->event_id !== $event->id) {
            \Log::error('Manual payment: Ticket mismatch', ['ticket_event' => $ticket->event_id, 'request_event' => $event->id]);
            return $this->error('Ticket not found for this event', 400);
        }

        if (!$ticket->is_active) {
            \Log::error('Manual payment: Ticket inactive', ['ticket_id' => $ticket->id]);
            return $this->error('Ticket sales are not active', 400);
        }

        if (!$ticket->isOnSale()) {
            \Log::error('Manual payment: Ticket not on sale', ['ticket_id' => $ticket->id]);
            return $this->error('Ticket sales are closed', 400);
        }

        $maxPerUser = $event->max_tickets_per_user;
        if ($maxPerUser && $validated['qty'] > $maxPerUser) {
            \Log::error('Manual payment: Max tickets exceeded', ['requested' => $validated['qty'], 'max' => $maxPerUser]);
            return $this->error('Maximum tickets per user exceeded', 400);
        }

        // Check availability
        if ($ticket->getAvailableQuantity() < $validated['qty']) {
            \Log::error('Manual payment: Not enough tickets', ['available' => $ticket->getAvailableQuantity(), 'requested' => $validated['qty']]);
            return $this->error('Not enough tickets available', 400);
        }

        if ($validated['qty'] <= 0) {
            \Log::error('Manual payment: Invalid quantity', ['qty' => $validated['qty']]);
            return $this->error('Invalid ticket quantity', 400);
        }

        // Check total tickets purchased (allow multiple purchases up to max)
        $confirmedQty = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('ticket_id', $ticket->id)
            ->where('status', 'confirmed')
            ->sum('qty');

        $maxPerUser = $event->max_tickets_per_user;
        if ($maxPerUser && ($confirmedQty + $validated['qty']) > $maxPerUser) {
            \Log::warning('Manual payment: Max tickets exceeded', [
                'user_id' => Auth::id(),
                'already_purchased' => $confirmedQty,
                'requesting' => $validated['qty'],
                'max_allowed' => $maxPerUser
            ]);
            $remaining = $maxPerUser - $confirmedQty;
            return $this->error("You can only purchase {$remaining} more ticket(s). You've already purchased {$confirmedQty}.", 400);
        }

        // Check for recent pending payment (within 1 hour)
        $pendingPayment = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('ticket_id', $ticket->id)
            ->where('status', 'pending_payment')
            ->where('created_at', '>', now()->subHour())
            ->first();

        if ($pendingPayment) {
            return $this->error('You have a pending payment. Please complete it or wait before trying again.', 400);
        }

        // Clean up old abandoned pending payments (>1 hour)
        EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending_payment')
            ->where('created_at', '<=', now()->subHour())
            ->each(function ($old) {
                EventCapacityLockService::releaseReservation($old);
                $old->payment?->delete();
                $old->delete();
            });

        $totalAmount = $ticket->price * $validated['qty'];

        $path = null;
        if ($request->hasFile('screenshot')) {
            $path = $request->file('screenshot')->store('event-payments', 'public');
        }

        $registration = null;
        $payment = null;
        $lockToken = null;
        $expiresAt = null;
        $transactionId = 'MAN-' . strtoupper(Str::random(12));

        try {
            DB::transaction(function () use (
                $event,
                $ticket,
                $validated,
                $totalAmount,
                $path,
                $transactionId,
                &$registration,
                &$payment,
                &$lockToken,
                &$expiresAt
            ) {
                $reserveResult = EventCapacityLockService::reserve(
                    $event,
                    $ticket,
                    (int) $validated['qty'],
                    (int) Auth::id()
                );

                if (!$reserveResult['success']) {
                    throw new \RuntimeException($reserveResult['error'] ?? 'Capacity reservation failed');
                }

                $lockToken = $reserveResult['data']['lock_token'];
                $expiresAt = $reserveResult['data']['expires_at'];

                $registration = EventRegistration::create([
                    'event_id' => $event->id,
                    'user_id' => Auth::id(),
                    'ticket_id' => $ticket->id,
                    'qty' => $validated['qty'],
                    'total_amount' => $totalAmount,
                    'status' => 'pending_payment',
                    'lock_token' => $lockToken,
                    'locked_at' => now(),
                    'payment_expires_at' => $expiresAt,
                ]);

                $payment = EventPayment::create([
                    'event_id' => $event->id,
                    'registration_id' => $registration->id,
                    'user_id' => Auth::id(),
                    'event_registration_id' => $registration->id,
                    'gateway' => 'manual',
                    'method' => $validated['method'],
                    'transaction_id' => $transactionId,
                    'sender_number' => $validated['sender_number'],
                    'trx_id' => $validated['trx_id'] ?? null,
                    'amount' => $totalAmount,
                    'currency' => 'BDT',
                    'status' => 'pending',
                    'verification_status' => 'pending',
                    'screenshot_path' => $path,
                ]);
            });
        } catch (\Exception $e) {
            return $this->error('Failed to submit payment: ' . $e->getMessage(), 500);
        }

        if (in_array($validated['method'], ['bkash', 'nagad', 'rocket'], true)) {
            $verified = PaymentVerificationService::verify(
                $validated['trx_id'],
                $validated['method'],
                $totalAmount
            );

            if (!$verified) {
                $payment->update([
                    'status' => 'failed',
                    'verification_status' => 'failed',
                ]);

                $transition = BookingStatusValidator::transitionStatus($registration, 'failed');
                if (!$transition['success']) {
                    \Log::warning('Failed to mark registration as failed', [
                        'registration_id' => $registration->id,
                        'error' => $transition['error'] ?? 'unknown',
                    ]);
                }

                return $this->error(
                    'Payment verification failed. Please check your transaction ID or contact support.',
                    400
                );
            }

            $payment->update([
                'status' => 'completed',
                'verification_status' => 'verified',
                'verified_at' => now(),
            ]);

            $transition = BookingStatusValidator::transitionStatus($registration, 'confirmed');
            if (!$transition['success']) {
                return $this->error($transition['error'] ?? 'Failed to confirm registration', 400);
            }

            return $this->created([
                'registration_id' => $registration->id,
                'payment_id' => $payment->id,
            ], 'Payment verified successfully!');
        }

        return $this->created([
            'registration_id' => $registration->id,
            'payment_id' => $payment->id,
            'expires_at' => $expiresAt,
        ], 'Payment submitted for review');
    }

    /**
     * Handle payment callback from SSLCommerz
     */
    public function callback(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaction_id' => 'required|string',
            'status' => 'required|in:success,failed,cancelled',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $payment = EventPayment::where('transaction_id', $validated['transaction_id'])->first();

                if (!$payment) {
                    return $this->notFound('Payment not found');
                }

                if ($validated['status'] === 'success') {
                    $payment->markAsCompleted();

                    // Increment ticket sold count
                    $registration = $payment->registration;
                    $registration->ticket->increment('sold_count', $registration->qty);

                    return $this->success([
                        'registration' => $registration->load('event'),
                    ], 'Payment successful');
                } else {
                    $payment->markAsFailed();
                    $payment->registration->markAsCancelled();

                    return $this->error('Payment failed', 400);
                }
            });
        } catch (\Exception $e) {
            return $this->error('Payment processing error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify payment status
     */
    public function verify(EventRegistration $registration): JsonResponse
    {
        if (Auth::id() !== $registration->user_id) {
            return $this->unauthorized('Unauthorized');
        }

        $payment = $registration->payment;

        if (!$payment) {
            return $this->notFound('Payment not found');
        }

        return $this->success([
            'status' => $payment->status,
            'registration' => $registration->load('event', 'ticket'),
        ], 'Payment status retrieved');
    }

    /**
     * Prepare SSLCommerz payment data
     */
    private function initiateSslcommerz(EventPayment $payment, EventRegistration $registration, float $amount): array
    {
        $storeId = config('payment.sslcommerz_store_id');
        $storePassword = config('payment.sslcommerz_store_password');
        $baseUrl = rtrim(config('payment.sslcommerz_base_url'), '/');

        if (!$storeId || !$storePassword) {
            return ['error' => 'SSLCommerz is not configured'];
        }

        $callbackBase = config('app.url');
        $successUrl = $callbackBase . '/events/' . $registration->event->slug . '/tickets?status=success&tran_id=' . $payment->transaction_id;
        $failUrl = $callbackBase . '/events/' . $registration->event->slug . '/tickets?status=failed&tran_id=' . $payment->transaction_id;
        $cancelUrl = $callbackBase . '/events/' . $registration->event->slug . '/tickets?status=cancelled&tran_id=' . $payment->transaction_id;

        $postData = [
            'store_id' => $storeId,
            'store_passwd' => $storePassword,
            'total_amount' => $amount,
            'currency' => 'BDT',
            'tran_id' => $payment->transaction_id,
            'success_url' => $successUrl,
            'fail_url' => $failUrl,
            'cancel_url' => $cancelUrl,
            'ipn_url' => route('events.payment.webhook.sslcommerz'),
            'cus_name' => Auth::user()->name,
            'cus_email' => Auth::user()->email,
            'cus_phone' => Auth::user()->phone ?? '01XXXXXXXXX',
            'cus_add1' => 'Bangladesh',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'product_name' => $registration->event->title . ' - ' . $registration->ticket->title,
            'product_category' => 'Event',
            'product_profile' => 'general',
        ];

        try {
            \Log::info('SSLCommerz payment initiation started', ['store_id' => $storeId, 'amount' => $amount]);
            
            $response = Http::asForm()->post($baseUrl . '/gwprocess/v4/api.php', $postData);
            $data = $response->json();

            // Store response as JSON string
            $payment->update([
                'raw_response' => json_encode($data),
            ]);

            // Check for HTTP errors
            if (!$response->successful()) {
                \Log::error('SSLCommerz HTTP error', [
                    'status' => $response->status(),
                    'reason' => $data['failedreason'] ?? 'No reason provided',
                ]);
                return ['error' => 'Payment setup failed: ' . ($data['failedreason'] ?? 'Gateway error')];
            }

            // Check for failed status in response
            if (isset($data['status']) && $data['status'] === 'FAILED') {
                \Log::error('SSLCommerz FAILED status', [
                    'reason' => $data['failedreason'] ?? 'Unknown',
                    'store_id' => $storeId,
                ]);
                return ['error' => 'Payment setup failed: ' . ($data['failedreason'] ?? 'Gateway rejected request')];
            }

            // Check if gateway URL is present
            if (empty($data['GatewayPageURL'])) {
                \Log::error('SSLCommerz missing GatewayPageURL', [
                    'response_status' => $data['status'] ?? 'unknown',
                    'has_error' => isset($data['error']),
                ]);
                return ['error' => 'Gateway did not provide a checkout URL. Please verify your credentials.'];
            }

            \Log::info('SSLCommerz payment initiated successfully', ['tran_id' => $payment->transaction_id]);

            return [
                'redirect_url' => $data['GatewayPageURL'],
                'session_key' => $data['sessionkey'] ?? null,
                'tran_id' => $payment->transaction_id,
            ];
        } catch (\Exception $e) {
            \Log::error('SSLCommerz exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ['error' => 'Payment gateway error: ' . $e->getMessage()];
        }
    }
}
