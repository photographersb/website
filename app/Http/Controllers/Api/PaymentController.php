<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    /**
     * Initiate payment
     */
    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|in:card,bkash,nagad,bank_transfer',
            'amount' => 'required|numeric|gt:0',
        ]);

        $booking = Booking::find($validated['booking_id']);

        // Check authorization
        if ($booking->client_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        // Check if booking is pending payment
        if ($booking->status !== 'pending_payment') {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking is not pending payment',
            ], 422);
        }

        $paymentMethod = $validated['payment_method'];

        // Route to appropriate payment gateway
        $response = match ($paymentMethod) {
            'card' => $this->processSSLCommerz($booking, $validated['amount']),
            'bkash' => $this->processBKash($booking, $validated['amount']),
            'nagad' => $this->processNagad($booking, $validated['amount']),
            'bank_transfer' => $this->initiateBankTransfer($booking, $validated['amount']),
        };

        return response()->json($response);
    }

    /**
     * Payment success callback
     */
    public function successCallback(Request $request)
    {
        $transactionId = $request->get('tran_id') ?? $request->get('transaction_id');

        try {
            $result = $this->paymentService->handleCallback($transactionId, 'success', $request->all());

            // Redirect to frontend success page
            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/payment/success?transaction=' . $transactionId);
        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/payment/error?message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Payment failure callback
     */
    public function failCallback(Request $request)
    {
        $transactionId = $request->get('tran_id') ?? $request->get('transaction_id');

        try {
            $this->paymentService->handleCallback($transactionId, 'failed', $request->all());

            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/payment/failed?transaction=' . $transactionId);
        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/payment/error?message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Payment cancellation callback
     */
    public function cancelCallback(Request $request)
    {
        $transactionId = $request->get('tran_id') ?? $request->get('transaction_id');

        try {
            $this->paymentService->handleCallback($transactionId, 'cancelled', $request->all());

            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/payment/cancelled?transaction=' . $transactionId);
        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/payment/error?message=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Get transaction details
     */
    public function getTransaction($transactionId)
    {
        $transaction = Transaction::with(['booking.photographer.user', 'user'])
            ->where('reference_id', $transactionId)
            ->firstOrFail();

        // Check authorization
        if ($transaction->user_id !== auth()->id() && $transaction->booking->photographer->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $transaction,
        ]);
    }

    /**
     * Get user's transactions
     */
    public function myTransactions(Request $request)
    {
        $query = Transaction::with(['booking.photographer.user'])
            ->where('user_id', auth()->id());

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $transactions,
        ]);
    }

}
