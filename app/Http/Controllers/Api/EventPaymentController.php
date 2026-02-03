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
use Illuminate\Support\Str;
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

        $validated = $request->validate([
            'ticket_id' => 'required|exists:event_tickets,id',
            'qty' => 'required|integer|min:1',
        ]);

        $ticket = EventTicket::findOrFail($validated['ticket_id']);

        // Verify ticket belongs to event
        if ($ticket->event_id !== $event->id) {
            return $this->error('Ticket not found for this event', 400);
        }

        // Check availability
        if ($ticket->getAvailableQuantity() < $validated['qty']) {
            return $this->error('Not enough tickets available', 400);
        }

        // Check if already registered
        $existing = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('ticket_id', $ticket->id)
            ->whereIn('status', ['confirmed', 'pending_payment'])
            ->first();

        if ($existing) {
            return $this->error('You already have a registration for this ticket type', 400);
        }

        $totalAmount = $ticket->price * $validated['qty'];

        try {
            return DB::transaction(function () use ($event, $ticket, $validated, $totalAmount) {
                // Create registration with pending_payment status
                $registration = EventRegistration::create([
                    'event_id' => $event->id,
                    'user_id' => Auth::id(),
                    'ticket_id' => $ticket->id,
                    'qty' => $validated['qty'],
                    'total_amount' => $totalAmount,
                    'status' => 'pending_payment',
                ]);

                // Create payment record
                $payment = EventPayment::create([
                    'event_registration_id' => $registration->id,
                    'gateway' => 'sslcommerz',
                    'transaction_id' => 'pending-' . Str::random(20),
                    'amount' => $totalAmount,
                    'currency' => 'BDT',
                    'status' => 'pending',
                ]);

                // TODO: Integrate with SSLCommerz
                // For now, return payment initialization data
                $paymentGatewayData = $this->prepareSSLCommerz(
                    $payment,
                    $registration,
                    $totalAmount
                );

                return $this->created([
                    'registration_id' => $registration->id,
                    'payment_gateway' => $paymentGatewayData,
                ], 'Payment initiated');
            });
        } catch (\Exception $e) {
            return $this->error('Failed to initiate payment: ' . $e->getMessage(), 500);
        }
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
    private function prepareSSLCommerz($payment, $registration, $amount)
    {
        // This is a stub - implement with actual SSLCommerz integration
        return [
            'store_id' => config('payment.ssl_store_id'),
            'store_passwd' => config('payment.ssl_store_password'),
            'total_amount' => $amount,
            'currency' => 'BDT',
            'tran_id' => $payment->transaction_id,
            'success_url' => route('events.payment.callback', ['transaction_id' => $payment->transaction_id]),
            'fail_url' => route('events.payment.failed'),
            'cancel_url' => route('events.payment.cancel'),
            'cus_name' => Auth::user()->name,
            'cus_email' => Auth::user()->email,
            'cus_phone' => Auth::user()->phone,
            'product_name' => $registration->event->title . ' - ' . $registration->ticket->title,
        ];
    }
}
