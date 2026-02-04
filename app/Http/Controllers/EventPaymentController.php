<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use Illuminate\Http\Request;

class EventPaymentController extends Controller
{
    /**
     * Handle Stripe webhook callback
     */
    public function stripeWebhook(Request $request)
    {
        // Verify webhook signature
        // In production: verify with Stripe API

        $event = $request->input('type');
        $data = $request->input('data.object', []);

        if ($event === 'payment_intent.succeeded') {
            // Update registration payment status
            $registrationId = $data['metadata']['registration_id'] ?? null;
            
            if ($registrationId) {
                EventRegistration::find($registrationId)?->update([
                    'payment_status' => 'paid',
                ]);
            }
        }

        return response()->json(['status' => 'received']);
    }

    /**
     * Handle SSLCommerz webhook callback
     */
    public function sslcommerzWebhook(Request $request)
    {
        // Verify webhook signature
        // In production: verify with SSLCommerz API

        $status = $request->input('status');
        $tran_id = $request->input('tran_id');

        // Find registration by transaction ID
        $registration = EventRegistration::where('payment_transaction_id', $tran_id)->first();

        if (!$registration) {
            return response()->json(['error' => 'Registration not found'], 404);
        }

        if ($status === 'VALID' || $status === 'COMPLETE') {
            $registration->update([
                'payment_status' => 'paid',
                'payment_completed_at' => now(),
            ]);
        } elseif ($status === 'FAILED') {
            $registration->update([
                'payment_status' => 'failed',
            ]);
        }

        return response()->json(['status' => 'received']);
    }
}
