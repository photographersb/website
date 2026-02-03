<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Booking;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    use ApiResponse;

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

        $booking = Booking::findOrFail($validated['booking_id']);

        if ($booking->client_id !== Auth::id()) {
            return $this->unauthorized('Unauthorized');
        }

        if ($booking->status !== 'pending_payment') {
            return $this->validationError(['booking' => 'Booking is not pending payment'], 'Booking is not pending payment');
        }

        $method = match ($validated['payment_method']) {
            'card' => 'sslcommerz',
            'bkash' => 'bkash',
            'nagad' => 'nagad',
            'bank_transfer' => 'bank',
        };

        try {
            $response = $this->paymentService->initiatePayment($booking, $method, $validated['amount']);
            return $this->success($response, 'Payment initiated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to initiate payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Payment success callback
     */
    public function successCallback(Request $request)
    {
        $transactionId = $request->get('tran_id') ?? $request->get('transaction_id');

        try {
            $this->paymentService->handleCallback($transactionId, 'completed', $request->all());

            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/payment/success?transaction=' . $transactionId);
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
    public function getTransaction(string $transactionId)
    {
        $transaction = Transaction::with(['booking.photographer.user', 'user'])
            ->where('reference_id', $transactionId)
            ->orWhere('transaction_id', $transactionId)
            ->firstOrFail();

        if ($transaction->user_id !== Auth::id() && $transaction->booking?->photographer?->user_id !== Auth::id()) {
            return $this->unauthorized('Unauthorized');
        }

        return $this->success($transaction, 'Transaction retrieved successfully');
    }

    /**
     * Get user's transactions
     */
    public function myTransactions(Request $request)
    {
        $query = Transaction::with(['booking.photographer.user'])
            ->where('user_id', Auth::id());

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->paginated($transactions, 'Transactions retrieved successfully');
    }

    /**
     * Process refund
     */
    public function refund(Request $request, string $transactionId)
    {
        if (!Auth::check()) {
            return $this->unauthorized('Unauthorized');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'refund_amount' => 'nullable|numeric|gt:0',
        ]);

        $transaction = Transaction::where('reference_id', $transactionId)
            ->orWhere('transaction_id', $transactionId)
            ->firstOrFail();

        if ($transaction->user_id !== Auth::id() && !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return $this->unauthorized('Unauthorized');
        }

        $refundAmount = $validated['refund_amount'] ?? $transaction->amount;

        if ($refundAmount > $transaction->amount) {
            return $this->validationError(['refund_amount' => 'Refund amount cannot exceed transaction amount'], 'Refund amount cannot exceed transaction amount');
        }

        try {
            $refundResult = $this->paymentService->processRefund($transaction, $refundAmount, $validated['reason']);

            return $this->success([
                'transaction_id' => $transaction->id,
                'refund_amount' => $refundAmount,
                'refund_status' => $refundResult['status'] ?? 'completed',
            ], 'Refund initiated successfully');
        } catch (\Exception $e) {
            return $this->validationError(['refund' => $e->getMessage()], $e->getMessage());
        }
    }
}