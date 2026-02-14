# SSLCommerz Integration Guide for Events

## Overview
The EventPaymentController is ready for SSLCommerz integration. Follow these steps to connect your payment gateway.

## Step 1: Install Dependencies

```bash
composer require sslwireless/sslcommerz-laravel
```

Or manually add to `composer.json` and run `composer update`.

## Step 2: Configure Environment

Add to `.env`:
```env
SSLCOMMERZ_STORE_ID=your_store_id
SSLCOMMERZ_STORE_PASSWORD=your_store_password
SSLCOMMERZ_API_URL=https://sandbox.sslcommerz.com
SSLCOMMERZ_SUCCESS_URL=/api/v1/payments/events/callback
SSLCOMMERZ_FAIL_URL=/api/v1/payments/events/callback
SSLCOMMERZ_CANCEL_URL=/api/v1/payments/events/callback
```

## Step 3: Create Payment Configuration File

Create `config/payment.php`:

```php
<?php

return [
    'sslcommerz' => [
        'store_id' => env('SSLCOMMERZ_STORE_ID'),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
        'api_url' => env('SSLCOMMERZ_API_URL', 'https://sandbox.sslcommerz.com'),
    ],

    'callbacks' => [
        'success_url' => env('SSLCOMMERZ_SUCCESS_URL', '/api/v1/payments/events/callback'),
        'fail_url' => env('SSLCOMMERZ_FAIL_URL', '/api/v1/payments/events/callback'),
        'cancel_url' => env('SSLCOMMERZ_CANCEL_URL', '/api/v1/payments/events/callback'),
    ],
];
```

## Step 4: Update EventPaymentController

Replace the `prepareSSLCommerz()` method in `app/Http/Controllers/Api/EventPaymentController.php`:

```php
private function prepareSSLCommerz(EventPayment $payment, EventRegistration $registration, EventTicket $ticket): array
{
    $event = $registration->event;
    $user = $registration->user;

    $postData = [
        'store_id' => config('payment.sslcommerz.store_id'),
        'store_passwd' => config('payment.sslcommerz.store_password'),
        'total_amount' => $payment->amount,
        'currency' => 'BDT',
        'tran_id' => $payment->transaction_id,
        'success_url' => route('payment.callback'),
        'fail_url' => route('payment.callback'),
        'cancel_url' => route('payment.callback'),
        'ipn_url' => route('payment.ipn'),
        'product_name' => $event->title,
        'product_category' => 'event-ticket',
        'product_profile' => 'event',
        'cus_name' => $user->name,
        'cus_email' => $user->email,
        'cus_phone' => $user->phone ?? '',
        'cus_add1' => 'Dhaka, Bangladesh',
        'ship_name' => $user->name,
        'ship_email' => $user->email,
        'ship_phone' => $user->phone ?? '',
        'ship_add1' => 'Dhaka, Bangladesh',
    ];

    return [
        'store_id' => $postData['store_id'],
        'store_passwd' => $postData['store_passwd'],
        'total_amount' => $postData['total_amount'],
        'currency' => $postData['currency'],
        'tran_id' => $postData['tran_id'],
        'success_url' => $postData['success_url'],
        'fail_url' => $postData['fail_url'],
        'cancel_url' => $postData['cancel_url'],
        'ipn_url' => $postData['ipn_url'],
        'product_name' => $postData['product_name'],
        'product_category' => $postData['product_category'],
        'product_profile' => $postData['product_profile'],
        'cus_name' => $postData['cus_name'],
        'cus_email' => $postData['cus_email'],
        'cus_phone' => $postData['cus_phone'],
        'cus_add1' => $postData['cus_add1'],
        'ship_name' => $postData['ship_name'],
        'ship_email' => $postData['ship_email'],
        'ship_phone' => $postData['ship_phone'],
        'ship_add1' => $postData['ship_add1'],
    ];
}
```

## Step 5: Handle Payment Callback

Update the `callback()` method to validate SSL response:

```php
public function callback(Request $request): JsonResponse
{
    $tranId = $request->tran_id;
    $payment = EventPayment::where('transaction_id', $tranId)->first();

    if (!$payment) {
        return response()->json(['error' => 'Payment not found'], 404);
    }

    // Verify with SSL using tranId
    $validation_url = config('payment.sslcommerz.api_url') . "/validator/api/validationApi.php";
    $validation_post_data = [
        'val_id' => $request->val_id,
        'store_id' => config('payment.sslcommerz.store_id'),
        'store_passwd' => config('payment.sslcommerz.store_password'),
        'format' => 'json',
    ];

    $response = Http::post($validation_url, $validation_post_data);
    $validation_result = $response->json();

    return DB::transaction(function () use ($payment, $validation_result, $request) {
        if ($validation_result['status'] === 'VALID' && $validation_result['transStatus'] === 'Successful') {
            $payment->markAsCompleted();
            $payment->update(['raw_response' => json_encode($validation_result)]);

            $registration = $payment->registration;
            $registration->markAsConfirmed();

            $ticket = $registration->ticket;
            if ($ticket) {
                $ticket->increment('sold_count', $registration->quantity);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment successful',
                'registration_id' => $registration->id,
            ]);
        } else {
            $payment->markAsFailed();
            $payment->update(['raw_response' => json_encode($validation_result)]);

            return response()->json([
                'success' => false,
                'message' => 'Payment failed',
            ], 400);
        }
    });
}
```

## Step 6: Add Routes for Callbacks

Add to `routes/api.php`:

```php
Route::post('/payments/events/callback', [EventPaymentController::class, 'callback'])->name('payment.callback');
Route::post('/payments/events/ipn', [EventPaymentController::class, 'ipn'])->name('payment.ipn');
```

## Step 7: Testing with SSLCommerz Sandbox

### Sandbox Credentials
- **Store ID**: `testbox`
- **Store Password**: `@1234`
- **API URL**: `https://sandbox.sslcommerz.com`

### Test Card Numbers
- **Visa**: 4111111111111111
- **MasterCard**: 5555555555554444

### Test Flow
1. Create event with paid tickets
2. Initiate payment via `/api/v1/payments/events/initiate`
3. Receive payment redirect URL
4. Complete payment on SSLCommerz sandbox
5. Redirect to callback handler
6. Payment marked as completed

## Step 8: Security Checklist

- [ ] Enable HTTPS in production
- [ ] Store sensitive config in `.env`
- [ ] Validate SSL responses server-side
- [ ] Use transaction IDs for idempotency
- [ ] Log all payment transactions
- [ ] Implement IPN endpoint for webhooks
- [ ] Rate limit payment endpoints
- [ ] Verify user ownership of registrations
- [ ] Test with real test cards before production
- [ ] Monitor for fraud/duplicate payments

## API Response Examples

### Initiate Payment Response
```json
{
  "payment_id": 1,
  "transaction_id": "EVT-1234567890",
  "amount": 1500,
  "currency": "BDT",
  "status": "pending",
  "redirect_url": "https://sandbox.sslcommerz.com/gwprocess/v4/gw.php?Q=..."
}
```

### Callback Success Response
```json
{
  "success": true,
  "message": "Payment successful",
  "registration_id": 123,
  "status": "confirmed",
  "ticket_details": {
    "event_id": 1,
    "event_title": "Bangladesh Wedding Expo",
    "ticket_type": "VIP Pass",
    "qr_token": "abc123def456..."
  }
}
```

### Callback Failure Response
```json
{
  "success": false,
  "message": "Payment failed",
  "error_code": "INVALID_VALIDATION"
}
```

## IPN Endpoint

The IPN (Instant Payment Notification) endpoint should also validate and process payments asynchronously:

```php
public function ipn(Request $request): Response
{
    // Log IPN request
    \Log::info('SSLCommerz IPN received', $request->all());

    // Same validation logic as callback
    $validation_result = $this->validateWithSSL($request->val_id);

    if ($validation_result['transStatus'] === 'Successful') {
        // Update payment status
        EventPayment::where('transaction_id', $request->tran_id)
            ->update(['status' => 'completed']);
    }

    return response('OK', 200);
}
```

## Troubleshooting

### Common Issues

1. **"Invalid Store ID"**
   - Check config/payment.php has correct values
   - Verify .env has SSLCOMMERZ_STORE_ID set

2. **"Amount Mismatch"**
   - Ensure currency is BDT
   - Amount is integer (in taka, not fractional)

3. **"Callback Not Received"**
   - Verify callback URL is publicly accessible
   - Check SSL certificate is valid
   - Verify firewall allows inbound requests

4. **"Transaction Already Processed"**
   - Implement idempotency check using transaction_id
   - Query existing payment before creating new one

## Production Checklist

- [ ] Update SSLCOMMERZ_API_URL to production URL
- [ ] Update SSLCOMMERZ_STORE_ID to production ID
- [ ] Update SSLCOMMERZ_STORE_PASSWORD to production password
- [ ] Enable HTTPS
- [ ] Set up proper error logging
- [ ] Test end-to-end payment flow
- [ ] Set up payment success/failure notifications
- [ ] Monitor transaction logs
- [ ] Implement refund functionality
- [ ] Set up reconciliation processes

---

## Support
For SSLCommerz support: https://www.sslcommerz.com/
For Laravel integration: Check Laravel documentation for HTTP requests

