<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeaturedPhotographer;
use App\Models\FeaturedPhotographerPayment;
use Illuminate\Http\Request;

class FeaturedPhotographerPaymentController extends Controller
{
    /**
     * Initiate payment for featured photographer listing
     */
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'featured_photographer_id' => 'required|exists:featured_photographers,id',
            'payment_method' => 'required|in:bkash,manual',
        ]);

        try {
            $featured = FeaturedPhotographer::findOrFail($validated['featured_photographer_id']);

            // Calculate price based on package tier
            $prices = [
                'Starter' => 999,
                'Professional' => 2499,
                'Enterprise' => 5999,
            ];

            $amount = $prices[$featured->package_tier] ?? 999;

            // Create payment record
            $payment = FeaturedPhotographerPayment::create([
                'featured_photographer_id' => $featured->id,
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
            ]);

            // Route to appropriate payment gateway
            if ($validated['payment_method'] === 'bkash') {
                return $this->initiateBkashPayment($payment, $featured);
            }

            return response()->json([
                'message' => 'Manual payment initiated',
                'data' => $payment,
                'instructions' => 'Please contact admin for manual payment details',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error initiating payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initiate bKash payment
     */
    private function initiateBkashPayment(FeaturedPhotographerPayment $payment, FeaturedPhotographer $featured)
    {
        try {
            // bKash API integration would go here
            // This is a placeholder - actual implementation depends on bKash API
            
            $bkashConfig = [
                'app_key' => config('payments.bkash.app_key'),
                'app_secret' => config('payments.bkash.app_secret'),
                'username' => config('payments.bkash.username'),
                'password' => config('payments.bkash.password'),
            ];

            // For now, return ready-to-pay response
            // In production, call actual bKash API
            return response()->json([
                'message' => 'bKash payment initiated',
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'status' => 'pending',
                'next_step' => 'Redirect user to bKash payment gateway',
                'checkout_url' => route('api.bkash.checkout', ['payment_id' => $payment->id]),
            ]);
        } catch (\Exception $e) {
            $payment->markAsFailed($e->getMessage());
            throw $e;
        }
    }


    /**
     * Handle bKash callback
     */
    public function bkashCallback(Request $request)
    {
        try {
            $paymentId = $request->input('paymentID');
            $status = $request->input('status');

            $payment = FeaturedPhotographerPayment::where('reference_number', $paymentId)->firstOrFail();

            if ($status === 'completed') {
                $payment->markAsCompleted($paymentId, $request->all());
                return response()->json(['message' => 'Payment successful', 'payment' => $payment]);
            } else {
                $payment->markAsFailed('bKash payment failed or cancelled');
                return response()->json(['message' => 'Payment failed', 'payment' => $payment], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error processing callback',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get payment details
     */
    public function show(FeaturedPhotographerPayment $payment)
    {
        return response()->json([
            'data' => $payment->load('featuredPhotographer.photographer')
        ]);
    }

    /**
     * Get all payments for a featured photographer
     */
    public function forFeaturedPhotographer(FeaturedPhotographer $featured)
    {
        $payments = $featured->payments()->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'data' => $payments->items(),
            'pagination' => [
                'total' => $payments->total(),
                'per_page' => $payments->perPage(),
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
            ]
        ]);
    }

    /**
     * Get payment statistics
     */
    public function statistics()
    {
        $stats = [
            'total_revenue' => FeaturedPhotographerPayment::completed()->sum('amount'),
            'this_month' => FeaturedPhotographerPayment::completed()
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('amount'),
            'pending_payments' => FeaturedPhotographerPayment::pending()->count(),
            'completed_payments' => FeaturedPhotographerPayment::completed()->count(),
            'by_method' => [
                'bkash' => FeaturedPhotographerPayment::byMethod('bkash')->completed()->sum('amount'),
                'manual' => FeaturedPhotographerPayment::byMethod('manual')->completed()->sum('amount'),
            ],
        ];

        return response()->json(['stats' => $stats]);
    }

    /**
     * Admin: Get all payments
     */
    public function adminIndex(Request $request)
    {
        $query = FeaturedPhotographerPayment::with('featuredPhotographer.photographer');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by payment method
        if ($request->has('method')) {
            $query->where('payment_method', $request->input('method'));
        }

        // Filter by date range
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('created_at', [
                $request->input('from_date'),
                $request->input('to_date')
            ]);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $payments->items(),
            'pagination' => [
                'total' => $payments->total(),
                'per_page' => $payments->perPage(),
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
            ]
        ]);
    }
}
