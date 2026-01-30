<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OTPService
{
    protected $smsGateway;
    protected $apiToken;

    public function __construct()
    {
        $this->smsGateway = env('SMS_GATEWAY', 'ssl_wireless');
        $this->apiToken = env('SMS_API_TOKEN');
    }

    public function generate($phone)
    {
        // Generate 6-digit OTP
        $otp = random_int(100000, 999999);
        
        // Store OTP in cache with 5 minute expiry
        $key = 'otp_' . $phone;
        Cache::put($key, [
            'otp' => hash('sha256', $otp),
            'attempts' => 0,
        ], now()->addMinutes(5));
        
        return $otp;
    }

    public function send($phone, $otp)
    {
        $message = "Your Photographar SB verification code is: {$otp}. Valid for 5 minutes. Do not share this code with anyone.";
        
        try {
            if ($this->smsGateway === 'ssl_wireless') {
                return $this->sendViaSSLWireless($phone, $message);
            } elseif ($this->smsGateway === 'bulksms') {
                return $this->sendViaBulkSMS($phone, $message);
            } else {
                // Log mode for testing
                Log::info("OTP for {$phone}: {$otp}");
                return true;
            }
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }

    private function sendViaSSLWireless($phone, $message)
    {
        // SSL Wireless Bangladesh API
        $url = 'https://smsplus.sslwireless.com/api/v3/send-sms';
        
        $data = [
            'api_token' => $this->apiToken,
            'sid' => env('SMS_SID', 'PHOTOGRAPHARSB'),
            'msisdn' => $this->formatPhoneBD($phone),
            'sms' => $message,
            'csms_id' => uniqid(),
        ];

        $response = \Http::post($url, $data);
        
        return $response->successful();
    }

    private function sendViaBulkSMS($phone, $message)
    {
        // BulkSMS Bangladesh API
        $url = 'https://bulksmsbd.net/api/smsapi';
        
        $data = [
            'api_key' => $this->apiToken,
            'type' => 'text',
            'number' => $this->formatPhoneBD($phone),
            'senderid' => env('SMS_SENDER_ID', '8809617611580'),
            'message' => $message,
        ];

        $response = \Http::post($url, $data);
        
        return $response->successful();
    }

    private function formatPhoneBD($phone)
    {
        // Remove any non-digit characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // Add Bangladesh country code if missing
        if (strlen($phone) === 11 && substr($phone, 0, 2) === '01') {
            $phone = '88' . $phone;
        }
        
        return $phone;
    }

    public function verify($phone, $otp)
    {
        $key = 'otp_' . $phone;
        $data = Cache::get($key);
        
        if (!$data) {
            return ['valid' => false, 'message' => 'OTP expired or not found'];
        }
        
        // Check attempts
        if ($data['attempts'] >= 3) {
            Cache::forget($key);
            return ['valid' => false, 'message' => 'Too many attempts. Request new OTP.'];
        }
        
        // Verify OTP
        if (hash('sha256', $otp) === $data['otp']) {
            Cache::forget($key);
            return ['valid' => true, 'message' => 'OTP verified successfully'];
        }
        
        // Increment attempts
        $data['attempts']++;
        Cache::put($key, $data, now()->addMinutes(5));
        
        return ['valid' => false, 'message' => 'Invalid OTP. ' . (3 - $data['attempts']) . ' attempts remaining.'];
    }

    public function resend($phone)
    {
        $otp = $this->generate($phone);
        return $this->send($phone, $otp);
    }
}
