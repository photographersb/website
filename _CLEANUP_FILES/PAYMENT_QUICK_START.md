vue-router.js?v=6b23f43c:207 [Vue Router warn]: No match found for location with path "/competitions/summer-photography-contest-2026"
warn$1 @ vue-router.js?v=6b23f43c:207
# Payment System - Quick Start Guide

## 🚀 Getting Started

### Prerequisites
- Laravel server running on `http://localhost:8000`
- Vite dev server running on `http://localhost:5173`
- Database seeded with test data
- User logged in (client account)

## 📋 Step-by-Step Test Guide

### Step 1: Create a Booking
1. Go to home page: `http://localhost:5173/`
2. Browse photographers
3. Click on any photographer card
4. Click "Book Now" button
5. Fill in booking form:
   - Event date (future date)
   - Location
   - Message
6. Submit form
7. ✅ Booking created with status "pending"

### Step 2: Navigate to Payment
1. After booking submission, you'll see "Proceed to Payment" button
2. OR go to My Bookings page
3. Click "Pay Now" on any pending booking
4. You'll be redirected to `/payment/{bookingId}`

### Step 3: Review Payment Details
The payment page shows:
- **Photographer Info:** Name, profile picture, specialty
- **Booking Details:** Event date, location, duration
- **Price Breakdown:**
  - Service Fee: ৳50,000
  - Advance Amount (30%): ৳15,000
  - Platform Fee (5% of advance): ৳750
  - **Total Amount: ৳15,750**
  - Remaining Balance: ৳35,000
- **Payment Methods:** 4 radio buttons

### Step 4: Select Payment Method

#### Option A: Card Payment (SSLCommerz)
1. Select "Credit/Debit Card (SSLCommerz)"
2. Click "Proceed to Payment"
3. Redirected to SSLCommerz sandbox page
4. Enter test card details:
   - Card Number: `4111111111111111`
   - Expiry: Any future date
   - CVV: Any 3 digits
5. Click Submit
6. ✅ Redirected to success page

#### Option B: bKash
1. Select "bKash Mobile Wallet"
2. Click "Proceed to Payment"
3. Currently in sandbox mode (auto-success)
4. Enter bKash number: `01711111111`
5. Enter PIN: `1234`
6. ✅ Payment successful

#### Option C: Nagad
1. Select "Nagad Mobile Wallet"
2. Click "Proceed to Payment"
3. Currently in sandbox mode (auto-success)
4. Enter Nagad number: `01711111111`
5. Enter PIN: `1234`
6. ✅ Payment successful

#### Option D: Bank Transfer
1. Select "Bank Transfer"
2. Click "Proceed to Payment"
3. See bank account details:
   ```
   Bank Name: Dutch-Bangla Bank Limited (DBBL)
   Account Name: Photographar Bangladesh
   Account Number: 1234567890
   Routing Number: 123456789
   Branch: Gulshan, Dhaka
   Reference: TXN123456
   ```
4. Copy details
5. ✅ Booking marked as "pending verification"

### Step 5: View Confirmation

After successful payment, you'll see:
- ✅ Green success icon
- "Payment Successful!" message
- Transaction details:
  - Transaction ID
  - Amount paid
  - Payment method
  - Date & time
  - Status badge
- Booking information:
  - Photographer name
  - Event date
  - Location
- Action buttons:
  - View My Bookings
  - Return to Home
  - Download Receipt

### Step 6: View Transaction History
1. Go to `/transactions`
2. See all your payments
3. Filter by:
   - Status (Pending, Completed, Failed, Cancelled)
   - Payment Method (Card, bKash, Nagad, Bank Transfer)
4. Click "View Details" on any transaction
5. See complete transaction information

## 🎯 Quick Test Scenarios

### Scenario 1: Successful Card Payment
```
Login → Browse → Select Photographer → Book Now → 
Fill Form → Submit → Pay Now → Select Card → 
Proceed → SSLCommerz Page → Enter Card → 
Submit → Success Page ✅
```

### Scenario 2: Bank Transfer
```
Login → Browse → Book → Fill Form → Submit → 
Pay Now → Select Bank Transfer → Proceed → 
Copy Bank Details → Manual Transfer → 
Pending Verification ⏳
```

### Scenario 3: View Transaction History
```
Login → Menu → Transactions → 
Filter by Status → View Details → 
Download Receipt 📄
```

## 🔍 Where to Find Things

### Frontend Pages
- Home: `http://localhost:5173/`
- Payment: `http://localhost:5173/payment/:bookingId`
- Success: `http://localhost:5173/payment/success?transaction=TXN123`
- Failed: `http://localhost:5173/payment/failed?transaction=TXN123`
- Cancelled: `http://localhost:5173/payment/cancelled?transaction=TXN123`
- Transactions: `http://localhost:5173/transactions`

### API Endpoints
- Initiate: `POST http://localhost:8000/api/v1/payments/initiate`
- Get Transaction: `GET http://localhost:8000/api/v1/payments/transactions/{id}`
- My Transactions: `GET http://localhost:8000/api/v1/payments/transactions`

### Callbacks
- Success: `http://localhost:8000/payment/callback/success`
- Failed: `http://localhost:8000/payment/callback/fail`
- Cancelled: `http://localhost:8000/payment/callback/cancel`

## 💡 Test Data

### Test User Credentials
```
Client Account:
Email: client@example.com
Password: password123

Photographer Account:
Email: photographer@example.com
Password: password123

Admin Account:
Email: admin@photographar.com
Password: password123
```

### Test Cards (SSLCommerz Sandbox)
```
Success Card:
- Number: 4111111111111111
- Expiry: 12/25
- CVV: 123

Failure Card:
- Number: 4242424242424242
- Expiry: 12/25
- CVV: 123
```

### Test Mobile Numbers (bKash/Nagad)
```
Success: 01711111111
Failed: 01811111111
PIN: 1234
```

## 🐛 Troubleshooting

### Payment Button Not Working
- Check if user is logged in
- Verify booking exists
- Check booking status (should be "pending")
- Open browser console for errors

### Gateway Redirect Not Working
- Check `.env` file has gateway credentials
- Verify `FRONTEND_URL` is set
- Check network tab for API errors
- Ensure both servers are running

### Callback Not Triggering
- Check callback routes in `web.php`
- Verify gateway callback URL configuration
- Check Laravel logs: `storage/logs/laravel.log`
- Ensure `APP_URL` matches server URL

### Transaction Not Showing
- Verify transaction was created in database
- Check if API call succeeded
- Look for JavaScript errors in console
- Check user authorization

## 📊 Database Checks

### View Transactions
```sql
SELECT * FROM transactions 
WHERE user_id = 2 
ORDER BY created_at DESC;
```

### View Booking Payment Status
```sql
SELECT id, client_id, photographer_id, 
       status, payment_status, 
       advance_amount, remaining_amount
FROM bookings 
WHERE id = 1;
```

### Check Transaction Status
```sql
SELECT reference_id, amount, payment_method, status
FROM transactions
WHERE reference_id = 'TXN123456';
```

## ✅ Success Indicators

After successful payment:
1. Transaction record created ✅
2. Transaction status = "completed" ✅
3. Booking payment_status = "completed" ✅
4. Booking status = "confirmed" ✅
5. Advance amount recorded ✅
6. User redirected to success page ✅
7. Transaction appears in history ✅

## 🔗 Related Files

### Backend
- `app/Services/PaymentService.php` - Payment logic
- `app/Http/Controllers/Api/PaymentController.php` - API endpoints
- `routes/api.php` - API routes
- `routes/web.php` - Callback routes
- `.env` - Gateway credentials

### Frontend
- `resources/js/components/PaymentCheckout.vue` - Payment form
- `resources/js/components/PaymentSuccess.vue` - Success page
- `resources/js/components/PaymentFailed.vue` - Failure page
- `resources/js/components/PaymentCancelled.vue` - Cancel page
- `resources/js/components/TransactionHistory.vue` - History list
- `resources/js/app.js` - Routes

### Documentation
- `docs/PAYMENT_SYSTEM.md` - Technical docs
- `PAYMENT_IMPLEMENTATION.md` - Implementation summary
- `PAYMENT_QUICK_START.md` - This file

## 🎓 Learning Resources

### SSLCommerz
- Docs: https://developer.sslcommerz.com/
- Sandbox: https://sandbox.sslcommerz.com/

### bKash
- Docs: https://developer.bka.sh/
- Sandbox: Contact bKash for credentials

### Nagad
- Docs: Contact Nagad for documentation
- Sandbox: Apply for merchant account

## 🚨 Important Notes

1. **Sandbox Mode:** All gateways currently in sandbox/test mode
2. **Test Credentials:** Using dummy credentials, won't work in production
3. **Bank Transfer:** Manual verification not yet implemented
4. **Email Notifications:** Not yet implemented (using log driver)
5. **Refunds:** Refund workflow not yet built

## 🎯 Next Features to Test

Once payment system is working:
1. Create multiple bookings
2. Make payments with different methods
3. View transaction history
4. Filter transactions by status
5. Download receipts
6. Test authorization (try accessing other user's transactions)
7. Test error scenarios (invalid booking ID, cancelled bookings)

## 📞 Need Help?

If something doesn't work:
1. Check both servers are running
2. Check browser console for errors
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify database has test data
5. Ensure user is logged in
6. Check `.env` configuration

---

**Happy Testing! 🎉**
