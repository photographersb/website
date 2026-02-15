# Payment System Documentation

## Overview
The Photographar platform integrates with multiple payment gateways to support various payment methods popular in Bangladesh:
- **SSLCommerz** - Credit/Debit card payments
- **bKash** - Mobile wallet (most popular in Bangladesh)
- **Nagad** - Mobile wallet (govt-backed)
- **Bank Transfer** - Manual bank transfer

## Architecture

### Payment Flow

```
1. Client creates a booking
2. Client navigates to payment page
3. Client selects payment method
4. System calculates: 
   - Service Fee (base price)
   - Advance Amount (30% of service fee)
   - Platform Fee (5% of advance)
   - Total = Advance + Platform Fee
   - Remaining = Service Fee - Advance
5. System initiates payment with selected gateway
6. Gateway redirects to payment page
7. Client completes payment
8. Gateway redirects to success/fail/cancel callback
9. System updates transaction and booking status
10. Client sees confirmation page
```

### File Structure

```
app/
├── Http/Controllers/Api/
│   └── PaymentController.php          # API endpoints
├── Services/
│   └── PaymentService.php             # Payment business logic
└── Models/
    ├── Transaction.php
    └── Booking.php

resources/js/components/
├── PaymentCheckout.vue                # Payment form
├── PaymentSuccess.vue                 # Success page
├── PaymentFailed.vue                  # Failure page
├── PaymentCancelled.vue               # Cancellation page
└── TransactionHistory.vue             # Transaction list

routes/
├── api.php                            # API routes
└── web.php                            # Callback routes
```

## API Endpoints

### 1. Initiate Payment
**POST** `/api/v1/payments/initiate`

**Request:**
```json
{
  "booking_id": 1,
  "payment_method": "card|bkash|nagad|bank_transfer",
  "amount": 15000.00
}
```

**Response (Card):**
```json
{
  "status": "success",
  "data": {
    "payment_method": "card",
    "gateway_url": "https://sandbox.sslcommerz.com/gwprocess/v4/api.php",
    "post_data": {
      "store_id": "test_store",
      "tran_id": "TXN123456",
      "total_amount": 15000,
      "currency": "BDT",
      ...
    }
  }
}
```

**Response (Bank Transfer):**
```json
{
  "status": "success",
  "data": {
    "payment_method": "bank_transfer",
    "instructions": {
      "bank_name": "Dutch-Bangla Bank Limited (DBBL)",
      "account_name": "Photographar Bangladesh",
      "account_number": "1234567890",
      "routing_number": "123456789",
      "branch": "Gulshan, Dhaka"
    },
    "transaction_reference": "TXN123456"
  }
}
```

### 2. Get Transaction Details
**GET** `/api/v1/payments/transactions/{transactionId}`

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "reference_id": "TXN123456",
    "transaction_type": "booking",
    "amount": 15000.00,
    "payment_method": "card",
    "status": "completed",
    "booking": {
      "id": 1,
      "event_date": "2025-02-15",
      "photographer": {
        "user": {
          "name": "John Doe"
        }
      }
    },
    "created_at": "2025-01-27T10:30:00"
  }
}
```

### 3. Get User Transactions
**GET** `/api/v1/payments/transactions`

**Query Parameters:**
- `status` - Filter by status (pending|completed|failed|cancelled)
- `payment_method` - Filter by method (card|bkash|nagad|bank_transfer)
- `page` - Page number
- `per_page` - Items per page (default: 15)

**Response:**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [...],
    "total": 25,
    "per_page": 15,
    "last_page": 2
  }
}
```

## Payment Gateway Callbacks

All gateways redirect to these routes after payment:

### Success Callback
**GET/POST** `/payment/callback/success`

**Query Parameters:**
- `tran_id` or `transaction_id` - Transaction reference ID

**Behavior:**
- Updates transaction status to "completed"
- Updates booking status to "confirmed"
- Updates booking payment_status to "completed"
- Redirects to frontend: `http://localhost:5173/payment/success?transaction={id}`

### Failure Callback
**GET/POST** `/payment/callback/fail`

**Behavior:**
- Updates transaction status to "failed"
- Keeps booking in pending state
- Redirects to frontend: `http://localhost:5173/payment/failed?transaction={id}`

### Cancel Callback
**GET/POST** `/payment/callback/cancel`

**Behavior:**
- Updates transaction status to "cancelled"
- Keeps booking in pending state
- Redirects to frontend: `http://localhost:5173/payment/cancelled?transaction={id}`

## Payment Methods

### 1. SSLCommerz (Card Payments)

**Configuration (.env):**
```env
SSLCOMMERZ_STORE_ID=your_store_id
SSLCOMMERZ_STORE_PASSWORD=your_password
SSLCOMMERZ_API_URL=https://sandbox.sslcommerz.com
SSLCOMMERZ_VALIDATION_URL=https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php
SSLCOMMERZ_MODE=sandbox  # or "live" for production
```

**Process:**
1. System creates transaction record
2. Builds post_data with store credentials
3. Returns gateway URL and form data to frontend
4. Frontend auto-submits form to SSLCommerz
5. User completes payment on SSLCommerz page
6. SSLCommerz redirects to callback URL
7. System verifies payment with validation API
8. Updates transaction and booking

**Testing:**
- Use sandbox credentials from SSLCommerz merchant portal
- Test cards available in SSLCommerz documentation

### 2. bKash (Mobile Wallet)

**Configuration (.env):**
```env
BKASH_APP_KEY=your_app_key
BKASH_APP_SECRET=your_app_secret
BKASH_USERNAME=your_username
BKASH_PASSWORD=your_password
BKASH_BASE_URL=https://tokenized.sandbox.bka.sh/v1.2.0-beta
BKASH_MODE=sandbox
```

**Process:**
1. Get grant token using app credentials
2. Create payment request with bKash API
3. Return payment URL to frontend
4. User enters bKash PIN on bKash page
5. bKash calls webhook with payment status
6. System verifies and updates records

**Status:** Currently mocked for development. Implement with bKash Tokenized Checkout API.

### 3. Nagad (Mobile Wallet)

**Configuration (.env):**
```env
NAGAD_MERCHANT_ID=your_merchant_id
NAGAD_MERCHANT_NUMBER=your_mobile_number
NAGAD_PUBLIC_KEY=your_public_key
NAGAD_PRIVATE_KEY=your_private_key
NAGAD_BASE_URL=http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs
NAGAD_MODE=sandbox
```

**Process:**
1. Initialize payment with Nagad API
2. Sign request with merchant private key
3. Return payment URL
4. User completes payment with Nagad PIN
5. Nagad calls callback with verification signature
6. System verifies with public key and updates

**Status:** Currently mocked for development. Implement with Nagad Payment Gateway API.

### 4. Bank Transfer (Manual)

**Configuration (.env):**
```env
BANK_NAME="Dutch-Bangla Bank Limited (DBBL)"
BANK_ACCOUNT_NAME="Photographar Bangladesh"
BANK_ACCOUNT_NUMBER="1234567890"
BANK_ROUTING_NUMBER="123456789"
BANK_BRANCH="Gulshan, Dhaka"
```

**Process:**
1. System returns bank account details
2. User manually transfers money
3. User uploads proof of payment (future feature)
4. Admin manually verifies and confirms (future feature)
5. System updates booking status

**Note:** Currently returns instructions. Admin verification workflow pending.

## Database Schema

### transactions table
```sql
- id (bigint)
- user_id (bigint) - Client who made payment
- transaction_type (varchar) - "booking"
- reference_id (varchar) - Unique transaction ID
- reference_table (bigint) - Booking ID
- amount (decimal)
- payment_method (enum) - card, bkash, nagad, bank_transfer
- status (enum) - pending, completed, failed, cancelled
- gateway_response (json) - Raw gateway response
- created_at
- updated_at
```

### bookings table (payment columns)
```sql
- payment_status (enum) - pending, partial, completed, refunded
- advance_amount (decimal) - Amount paid upfront
- remaining_amount (decimal) - Balance to be paid
```

## Frontend Components

### PaymentCheckout.vue
- Displays booking summary
- Shows photographer details
- Calculates payment breakdown:
  - Service fee
  - 30% advance
  - 5% platform fee
  - Remaining balance
- Payment method selection
- Submits to `/payments/initiate`
- Handles gateway redirect

### PaymentSuccess.vue
- Shows success icon and message
- Displays transaction details
- Shows booking information
- Download receipt button
- Navigation to bookings/home

### PaymentFailed.vue
- Shows error icon and message
- Lists common failure reasons
- Retry payment button
- Contact support link

### PaymentCancelled.vue
- Shows warning icon
- Explains reservation status
- Complete payment button
- View bookings button

### TransactionHistory.vue
- Lists all user transactions
- Filters by status and method
- Pagination support
- Status badges with colors
- Links to transaction details

## Testing

### Test Flow (Sandbox Mode)

1. **Login:**
   ```
   Email: client@example.com
   Password: password123
   ```

2. **Create Booking:**
   - Browse photographers
   - Select a photographer
   - Fill booking form
   - Submit inquiry

3. **Make Payment:**
   - Navigate to booking
   - Click "Pay Now"
   - Select payment method
   - Complete payment

4. **SSLCommerz Test Cards:**
   - Success: Any card with valid format
   - Failure: Use specific test cards from SSLCommerz docs

5. **Bank Transfer:**
   - Displays account details
   - Currently auto-marks as pending
   - Admin approval needed (future)

### Manual Testing Checklist

- [ ] Payment initiation with all 4 methods
- [ ] Success callback handling
- [ ] Failure callback handling
- [ ] Cancel callback handling
- [ ] Transaction history pagination
- [ ] Transaction filtering
- [ ] Amount calculations (advance 30%, platform 5%)
- [ ] Booking status updates
- [ ] Transaction record creation
- [ ] Receipt download
- [ ] Error handling for invalid bookings
- [ ] Authorization checks (own booking only)
- [ ] Gateway URL generation
- [ ] Callback verification

## Security Considerations

1. **Authorization:**
   - Only booking owner can initiate payment
   - Transaction details only visible to involved parties
   - Middleware protects all endpoints

2. **Validation:**
   - All amounts validated server-side
   - Booking status checked before payment
   - Duplicate payments prevented
   - Gateway responses verified

3. **PCI Compliance:**
   - No card data stored
   - All card processing via SSLCommerz
   - HTTPS required for production

4. **Webhook Security:**
   - Verify gateway signatures
   - Check transaction IDs match
   - Validate amounts
   - Use HTTPS for callbacks

## Production Checklist

- [ ] Replace sandbox credentials with live credentials
- [ ] Set `SSLCOMMERZ_MODE=live`
- [ ] Set `BKASH_MODE=live`
- [ ] Set `NAGAD_MODE=live`
- [ ] Update bank account details with real account
- [ ] Enable HTTPS for entire site
- [ ] Configure proper callback URLs (production domain)
- [ ] Test all gateways in live mode
- [ ] Set up email notifications for payments
- [ ] Configure webhook IP whitelisting
- [ ] Set up transaction monitoring/alerts
- [ ] Configure automatic receipt generation (PDF)
- [ ] Set up refund workflow
- [ ] Enable transaction logs/audit trail
- [ ] Configure fraud detection rules
- [ ] Set payment limits and thresholds
- [ ] Add payment retry logic
- [ ] Implement payment timeout handling

## Future Enhancements

1. **Partial Payments:**
   - Allow multiple installment payments
   - Track payment schedule
   - Send reminders for due payments

2. **Refunds:**
   - Cancellation refund workflow
   - Partial/full refund options
   - Refund to original payment method

3. **Bank Transfer Verification:**
   - Upload payment proof
   - Admin approval workflow
   - Automatic verification via bank API

4. **Payment Analytics:**
   - Revenue dashboard
   - Payment success rates
   - Popular payment methods
   - Transaction trends

5. **Saved Payment Methods:**
   - Save card/wallet for future
   - One-click payments
   - Tokenization support

6. **International Payments:**
   - PayPal integration
   - Stripe for cards
   - Multi-currency support

7. **Invoice Generation:**
   - Professional PDF invoices
   - Email delivery
   - Tax calculations

## Support

For payment gateway integration issues:

- **SSLCommerz:** support@sslcommerz.com
- **bKash:** merchant.support@bkash.com
- **Nagad:** merchantsupport@mynagad.com

For platform issues:
- Email: dev@photographar.com
- Documentation: /docs/payment-system.md
