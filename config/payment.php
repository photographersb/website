<?php

return [
    // bKash Configuration
    'bkash_app_key' => env('BKASH_APP_KEY'),
    'bkash_app_secret' => env('BKASH_APP_SECRET'),
    'bkash_username' => env('BKASH_USERNAME'),
    'bkash_password' => env('BKASH_PASSWORD'),
    'bkash_base_url' => env('BKASH_BASE_URL', 'https://checkout.bkash.com'),

    // Nagad Configuration
    'nagad_merchant_id' => env('NAGAD_MERCHANT_ID'),
    'nagad_merchant_key' => env('NAGAD_MERCHANT_KEY'),
    'nagad_base_url' => env('NAGAD_BASE_URL', 'https://api.nagad.com.bd'),

    // uPay Configuration
    'upay_merchant_id' => env('UPAY_MERCHANT_ID'),
    'upay_api_key' => env('UPAY_API_KEY'),
    'upay_base_url' => env('UPAY_BASE_URL', 'https://api.upay.com.bd'),

    // SSLCommerz Configuration
    'sslcommerz_store_id' => env('SSLCOMMERZ_STORE_ID'),
    'sslcommerz_store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
    'sslcommerz_base_url' => env('SSLCOMMERZ_BASE_URL', 'https://sandbox.sslcommerz.com'),

    // General Payment Settings
    'currency' => env('PAYMENT_CURRENCY', 'BDT'),
    'webhook_timeout' => env('PAYMENT_WEBHOOK_TIMEOUT', 30),
];
