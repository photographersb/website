<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Booking;
use App\Notifications\PaymentReceived;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Initiate payment
     */
    public function initiatePayment(Booking $booking, string $method, float $amount): array
    {
        $booking->loadMissing('photographer');
        $normalizedMethod = match ($method) {
            'sslcommerz' => 'card',
            'bank' => 'bank_transfer',
            default => $method,
        };

        // Create transaction record
        $transaction = Transaction::create([
            'booking_id' => $booking->id,
            'photographer_id' => $booking->photographer?->user_id,
            'user_id' => auth()->id(),
            'transaction_type' => 'booking',
            'payment_method' => $normalizedMethod,
            'reference_id' => (string) $booking->id,
            'reference_table' => 'bookings',
            'amount' => $amount,
            'currency' => 'BDT',
            'status' => 'pending',
            'transaction_id' => 'TXN' . strtoupper(Str::random(12)),
            'net_amount' => $amount,
        ]);

        // Route to appropriate payment gateway
        switch ($method) {
            case 'sslcommerz':
                return $this->initiateSslCommerz($transaction);
            case 'bkash':
                return $this->initiateBkash($transaction);
            case 'nagad':
                return $this->initiateNagad($transaction);
            case 'bank':
                return $this->initiateBankTransfer($transaction);
            default:
                throw new \Exception('Invalid payment method');
        }
    }

    /**
     * SSLCommerz integration
     */
    private function initiateSslCommerz(Transaction $transaction): array
    {
        $transaction->loadMissing('user');
        $post_data = [
            'store_id' => config('services.sslcommerz.store_id'),
            'store_passwd' => config('services.sslcommerz.store_password'),
            'total_amount' => $transaction->amount,
            'currency' => 'BDT',
            'tran_id' => $transaction->transaction_id,
            'success_url' => route('payment.callback.success'),
            'fail_url' => route('payment.callback.fail'),
            'cancel_url' => route('payment.callback.cancel'),
            'cus_name' => $transaction->user->name,
            'cus_email' => $transaction->user->email,
            'cus_phone' => $transaction->user->phone,
            'cus_add1' => 'Bangladesh',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'product_name' => 'Photography Booking',
            'product_category' => 'Service',
            'product_profile' => 'general',
        ];

        // In production, make actual API call
        // For now, return mock gateway URL
        $gateway_url = config('services.sslcommerz.sandbox_url') . '/gwprocess/v4/api.php?' . http_build_query($post_data);

        return [
            'transaction_id' => $transaction->transaction_id,
            'gateway_url' => $gateway_url,
            'method' => 'redirect',
        ];
    }

    /**
     * bKash integration
     */
    private function initiateBkash(Transaction $transaction): array
    {
        // Mock bKash integration
        // In production, integrate with bKash API
        
        return [
            'transaction_id' => $transaction->transaction_id,
            'gateway_url' => config('services.bkash.checkout_url'),
            'method' => 'redirect',
        ];
    }

    /**
     * Nagad integration
     */
    private function initiateNagad(Transaction $transaction): array
    {
        // Mock Nagad integration
        // In production, integrate with Nagad API
        
        return [
            'transaction_id' => $transaction->transaction_id,
            'gateway_url' => config('services.nagad.checkout_url'),
            'method' => 'redirect',
        ];
    }

    /**
     * Bank transfer
     */
    private function initiateBankTransfer(Transaction $transaction): array
    {
        return [
            'transaction_id' => $transaction->transaction_id,
            'method' => 'manual',
            'instructions' => [
                'account_name' => 'Photographar Ltd',
                'account_number' => '1234567890',
                'bank_name' => 'Dutch Bangla Bank',
                'branch' => 'Gulshan, Dhaka',
                'reference' => $transaction->transaction_id,
            ],
        ];
    }

    /**
     * Handle payment callback
     */
    public function handleCallback(string $transactionId, string $status, array $data = []): Transaction
    {
        $transaction = Transaction::where('reference_id', $transactionId)
            ->orWhere('transaction_id', $transactionId)
            ->firstOrFail();

        $transaction->update([
            'status' => $status,
            'gateway_response' => $data,
            'completed_at' => $status === 'completed' ? now() : null,
        ]);

        // Update booking status and send notifications if payment successful
        $transaction->loadMissing('booking');
        if ($status === 'completed' && $transaction->booking) {
            $transaction->booking->update([
                'status' => 'confirmed',
                'payment_status' => 'completed',
            ]);

            // Send payment confirmation emails
            $transaction->load(['booking.client', 'booking.photographer.user']);
            $transaction->booking->client->notify(new PaymentReceived($transaction, 'client'));
            $transaction->booking->photographer->user->notify(new PaymentReceived($transaction, 'photographer'));
        }

        return $transaction;
    }

    /**
     * Verify payment
     */
    public function verifyPayment(string $transactionId): bool
    {
        // In production, verify with payment gateway
        // For now, return true for testing
        return true;
    }

    /**
     * Process refund for a transaction
     * 
     * @param Transaction $transaction The transaction to refund
     * @param float $amount The amount to refund
     * @param string $reason The reason for refund
     * @return array Refund result with status and details
     */
    public function processRefund(Transaction $transaction, float $amount, string $reason): array
    {
        // Validate refund amount
        if ($amount > $transaction->amount) {
            throw new \Exception('Refund amount cannot exceed transaction amount');
        }

        // Check if transaction is refundable
        if ($transaction->status !== 'completed') {
            throw new \Exception('Only completed transactions can be refunded');
        }

        // Check if already refunded
        if ($transaction->refund_status === 'completed') {
            throw new \Exception('Transaction has already been refunded');
        }

        // Route to appropriate refund handler based on payment method
        $result = match($transaction->payment_method) {
            'sslcommerz' => $this->refundSslCommerz($transaction, $amount, $reason),
            'bkash' => $this->refundBkash($transaction, $amount, $reason),
            'nagad' => $this->refundNagad($transaction, $amount, $reason),
            'bank' => $this->refundBankTransfer($transaction, $amount, $reason),
            default => throw new \Exception('Refund not supported for this payment method')
        };

        // Update transaction with refund details
        $transaction->update([
            'refund_amount' => $amount,
            'refund_status' => $result['status'],
            'refund_reason' => $reason,
            'refund_reference' => $result['refund_reference'] ?? null,
            'refunded_at' => $result['status'] === 'completed' ? now() : null,
        ]);

        // Update booking status if full refund
        if ($amount >= $transaction->amount && $transaction->booking) {
            $transaction->booking->update([
                'payment_status' => 'refunded',
                'status' => 'cancelled',
            ]);
        }

        return $result;
    }

    /**
     * Refund via SSLCommerz
     */
    private function refundSslCommerz(Transaction $transaction, float $amount, string $reason): array
    {
        // SSLCommerz Refund API Integration
        try {
            $data = [
                'refund_amount' => $amount,
                'refund_remarks' => $reason,
                'bank_tran_id' => $transaction->gateway_transaction_id ?? $transaction->transaction_id,
                'store_id' => config('services.sslcommerz.store_id'),
                'store_passwd' => config('services.sslcommerz.store_password'),
            ];

            // In production, call SSLCommerz Refund API
            // $response = Http::post(config('services.sslcommerz.refund_url'), $data);
            
            // Mock response for development
            $mockSuccess = true;

            if ($mockSuccess) {
                return [
                    'status' => 'completed',
                    'refund_reference' => 'REF' . strtoupper(Str::random(10)),
                    'message' => 'Refund processed successfully via SSLCommerz',
                    'gateway_response' => ['status' => 'success', 'amount' => $amount]
                ];
            }

            return [
                'status' => 'failed',
                'message' => 'SSLCommerz refund failed',
                'gateway_response' => []
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'SSLCommerz refund error: ' . $e->getMessage(),
                'gateway_response' => []
            ];
        }
    }

    /**
     * Refund via bKash
     */
    private function refundBkash(Transaction $transaction, float $amount, string $reason): array
    {
        // bKash Refund API Integration
        try {
            // In production, integrate with bKash Refund API
            // 1. Get auth token
            // 2. Call refund transaction API
            // 3. Handle response

            // Mock response for development
            $mockSuccess = true;

            if ($mockSuccess) {
                return [
                    'status' => 'completed',
                    'refund_reference' => 'BKR' . strtoupper(Str::random(10)),
                    'message' => 'Refund processed successfully via bKash',
                    'gateway_response' => ['transactionStatus' => 'Completed', 'amount' => $amount]
                ];
            }

            return [
                'status' => 'failed',
                'message' => 'bKash refund failed',
                'gateway_response' => []
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'bKash refund error: ' . $e->getMessage(),
                'gateway_response' => []
            ];
        }
    }

    /**
     * Refund via Nagad
     */
    private function refundNagad(Transaction $transaction, float $amount, string $reason): array
    {
        // Nagad Refund API Integration
        try {
            // In production, integrate with Nagad Refund API
            // Similar to bKash implementation

            // Mock response for development
            $mockSuccess = true;

            if ($mockSuccess) {
                return [
                    'status' => 'completed',
                    'refund_reference' => 'NGR' . strtoupper(Str::random(10)),
                    'message' => 'Refund processed successfully via Nagad',
                    'gateway_response' => ['status' => 'Success', 'amount' => $amount]
                ];
            }

            return [
                'status' => 'failed',
                'message' => 'Nagad refund failed',
                'gateway_response' => []
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'Nagad refund error: ' . $e->getMessage(),
                'gateway_response' => []
            ];
        }
    }

    /**
     * Refund via Bank Transfer (Manual Process)
     */
    private function refundBankTransfer(Transaction $transaction, float $amount, string $reason): array
    {
        // Bank transfer refunds are manual
        // Create a pending refund request for admin to process
        
        return [
            'status' => 'pending',
            'refund_reference' => 'BTR' . strtoupper(Str::random(10)),
            'message' => 'Bank transfer refund initiated. Admin will process within 3-5 business days.',
            'gateway_response' => [
                'method' => 'manual',
                'instructions' => 'Please provide your bank account details to receive the refund.'
            ]
        ];
    }
}
