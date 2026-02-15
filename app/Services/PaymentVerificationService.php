<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentVerificationService
{
    /**
     * Verify payment with gateway
     */
    public static function verify(string $transactionId, string $method, float $amount): bool
    {
        try {
            if ($method === 'bkash') {
                return self::verifyBkash($transactionId, $amount);
            }
            if ($method === 'nagad') {
                return self::verifyNagad($transactionId, $amount);
            }
            if ($method === 'rocket') {
                return self::verifyRocket($transactionId, $amount);
            }
            
            Log::warning("Unknown payment method: {$method}");
            return false;
        } catch (\Exception $e) {
            Log::error("Payment verification error: {$e->getMessage()}", [
                'transaction_id' => $transactionId,
                'method' => $method,
                'amount' => $amount,
            ]);
            return false;
        }
    }

    /**
     * Verify Bkash transaction
     */
    private static function verifyBkash(string $txnId, float $amount): bool
    {
        try {
            // In production, call actual Bkash API
            // For now, return verification success only for demo
            if (config('app.debug')) {
                Log::info("Bkash verification (debug mode)", [
                    'transaction_id' => $txnId,
                    'amount' => $amount,
                ]);
                return true;
            }

            $response = Http::asForm()
                ->timeout(10)
                ->post(config('payment.bkash_verify_url') ?? 'https://api.bkash.com/verify', [
                    'app_key' => config('payment.bkash_app_key'),
                    'app_secret' => config('payment.bkash_app_secret'),
                    'transactionId' => $txnId,
                ])
                ->json();

            return isset($response['statusCode']) && 
                   $response['statusCode'] === '0000' && 
                   $response['amount'] == $amount;
        } catch (\Exception $e) {
            Log::error("Bkash verification failed: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Verify Nagad transaction
     */
    private static function verifyNagad(string $txnId, float $amount): bool
    {
        try {
            if (config('app.debug')) {
                return true;
            }

            $response = Http::asForm()
                ->timeout(10)
                ->post(config('payment.nagad_verify_url') ?? 'https://api.nagad.com/verify', [
                    'merchant_id' => config('payment.nagad_merchant_id'),
                    'merchant_key' => config('payment.nagad_merchant_key'),
                    'order_id' => $txnId,
                ])
                ->json();

            return isset($response['status']) && 
                   $response['status'] === 'success' && 
                   $response['amount'] == $amount;
        } catch (\Exception $e) {
            Log::error("Nagad verification failed: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Verify Rocket transaction
     */
    private static function verifyRocket(string $txnId, float $amount): bool
    {
        try {
            if (config('app.debug')) {
                return true;
            }

            $response = Http::asForm()
                ->timeout(10)
                ->post(config('payment.rocket_verify_url') ?? 'https://api.rocket.com/verify', [
                    'customer_id' => config('payment.rocket_customer_id'),
                    'password' => config('payment.rocket_password'),
                    'mon_id' => $txnId,
                ])
                ->json();

            return isset($response['statusCode']) && 
                   $response['statusCode'] === '0000' && 
                   $response['amount'] == $amount;
        } catch (\Exception $e) {
            Log::error("Rocket verification failed: {$e->getMessage()}");
            return false;
        }
    }
}
