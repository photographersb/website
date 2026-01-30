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
        // Create transaction record
        $transaction = Transaction::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'transaction_type' => 'booking_payment',
            'payment_method' => $method,
            'amount' => $amount,
            'currency' => 'BDT',
            'status' => 'pending',
            'transaction_id' => 'TXN' . strtoupper(Str::random(12)),
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
            'gateway_response' => json_encode($data),
            'completed_at' => $status === 'completed' ? now() : null,
        ]);

        // Update booking status and send notifications if payment successful
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
}
