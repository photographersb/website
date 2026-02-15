# 🔧 360° AUDIT - PHASE 1 IMPLEMENTATION GUIDE
## Critical Fixes (Must Be Done Before Deployment)

**Duration**: 4-6 hours  
**Testing Duration**: 2-3 hours  
**Total**: ~8 hours

---

# ⚠️ PRIORITY 1: Event Payment Verification (Issue #E1)

## Impact: CRITICAL - $$$
Users can gain free access to paid events by submitting zero payment

### Step 1: Update Event Payment Table
```php
// database/migrations/2026_02_16_add_payment_verification.php
php artisan make:migration add_payment_verification_fields_to_event_payments_table

// In migration:
Schema::table('event_payments', function (Blueprint $table) {
    $table->string('transaction_id')->unique()->under('payment_method'); // For verification
    $table->string('verification_status')->default('pending'); // pending, verified, failed
    $table->text('gateway_response')->nullable(); // Store response
    $table->timestamp('verified_at')->nullable();
});
```

Run Migration:
```bash
php artisan migrate
```

### Step 2: Create Payment Verification Service
```php
// app/Services/PaymentVerificationService.php
<?php
namespace App\Services;

class PaymentVerificationService
{
    /**
     * Verify payment with gateway
     */
    public static function verify(string $transactionId, string $method, float $amount)
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
            return false;
        } catch (\Exception $e) {
            \Log::error("Payment verification error: {$e->getMessage()}");
            return false;
        }
    }
    
    private static function verifyBkash($txnId, $amount)
    {
        // Call Bkash API
        $response = \Http\Client\Factory::asForm()
            ->post(config('payment.bkash_verify_url'), [
                'app_key' => config('payment.bkash_app_key'),
                'app_secret' => config('payment.bkash_app_secret'),
                'transactionId' => $txnId,
            ])->json();
        
        return $response['statusCode'] == '0000' && 
               $response['amount'] == $amount;
    }
    
    // Similar for Nagad, Rocket...
}
```

### Step 3: Update Event Payment Controller
```php
// app/Http/Controllers/Api/EventPaymentController.php
// Find method: public function manual()

public function manual(Request $request, Event $event)
{
    $validated = $request->validate([
        'amount' => [
            'required',
            'numeric',
            'min:' . $event->base_price,
            new ValidateEventPaymentAmount($event),
        ],
        'method' => 'required|in:bkash,nagad,rocket,card',
        'transaction_id' => 'required|string|unique:event_payments|max:100',
    ]);
    
    // Verify with payment gateway FIRST
    $verified = \App\Services\PaymentVerificationService::verify(
        $validated['transaction_id'],
        $validated['method'],
        $validated['amount']
    );
    
    if (!$verified) {
        \Log::warning("Payment verification failed", [
            'transaction_id' => $validated['transaction_id'],
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
        ]);
        
        return $this->error(
            'Payment verification failed. Please check your transaction ID or contact support.',
            400
        );
    }
    
    // ONLY NOW create payment record
    DB::transaction(function () use ($event, $validated, $request) {
        $payment = EventPayment::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'payment_method' => $validated['method'],
            'transaction_id' => $validated['transaction_id'],
            'verification_status' => 'verified',
            'verified_at' => now(),
            'gateway_response' => json_encode($verified), // Store response
            'status' => 'completed',
        ]);
        
        // Register user
        if (!EventRegistration::where('event_id', $event->id)
            ->where('user_id', auth()->id())->exists()) {
            EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => auth()->id(),
                'registration_code' => Str::random(8),
                'payment_id' => $payment->id,
                'status' => 'confirmed',
            ]);
        }
    });
    
    return $this->success([
        'payment_id' => $payment->id,
        'ticket_qr' => $payment->registration->ticket_qr_path ?? null,
    ], 'Payment verified successfully!');
}
```

### Test It
```bash
# Test verification - should reject invalid transaction
curl -X POST http://localhost/api/v1/events/1/payments/manual \
  -H "Authorization: Bearer TOKEN" \
  -d '{
    "amount": 500,
    "method": "bkash",
    "transaction_id": "FAKE12345"
  }'

# Should respond with error
```

---

# ⚠️ PRIORITY 2: Event Registration Capacity (Issue #E2)

## Impact: CRITICAL - Overbooking
Users can register beyond capacity

### Step 1: Use Database Locking
```php
// app/Http/Controllers/Api/EventController.php
// Find: public function rsvp()

public function rsvp(Request $request, Event $event)
{
    return DB::transaction(function () use ($event) {
        // Lock this event record until transaction completes
        $lockedEvent = Event::lockForUpdate()
            ->findOrFail($event->id);
        
        // Check capacity
        $registrationCount = EventRegistration::where('event_id', $lockedEvent->id)
            ->count();
        
        if ($registrationCount >= $lockedEvent->max_attendees) {
            throw new \Exception('Event is full');
        }
        
        // Check if already registered
        if (EventRegistration::where('event_id', $lockedEvent->id)
            ->where('user_id', auth()->id())->exists()) {
            throw new \Exception('Already registered');
        }
        
        // Register
        $registration = EventRegistration::create([
            'event_id' => $lockedEvent->id,
            'user_id' => auth()->id(),
            'registration_code' => Str::random(8),
            'status' => 'confirmed',
        ]);
        
        return $registration;
    }, retries: 3); // Retry if deadlock
}
```

### Step 2: Add Database Constraint (Belt & Suspenders)
```php
// database/migrations/2026_02_16_add_event_capacity_constraint.php
php artisan make:migration add_event_capacity_constraint

// In migration:
DB::statement(
    "ALTER TABLE event_registrations 
    ADD CONSTRAINT check_event_capacity 
    CHECK (
        (SELECT COUNT(*) FROM event_registrations WHERE event_id = event_registrations.event_id) 
        <= (SELECT max_attendees FROM events WHERE id = event_registrations.event_id)
    )"
);
```

### Test It
```bash
# Send 2 concurrent requests to register for event with capacity 1
for i in {1..2}; do
  curl -X POST "http://localhost/api/v1/events/1/rsvp" \
    -H "Authorization: Bearer USER$i" &
done
wait

# One should succeed, one should error
```

---

# ⚠️ PRIORITY 3: Booking Status Validator (Issue #B1)

## Impact: CRITICAL - Data Integrity
Any booking status can be set, causing invalid states

### Step 1: Create State Machine
```php
// app/Support/BookingStateMachine.php
<?php
namespace App\Support;

class BookingStateMachine
{
    // Define valid transitions
    private static array $transitions = [
        'pending' => ['accepted', 'cancelled'],
        'accepted' => ['completed', 'cancelled'],
        'cancelled' => [],
        'completed' => [],
    ];
    
    /**
     * Check if transition valid
     */
    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::$transitions[$from] ?? []);
    }
    
    /**
     * Get allowed next states
     */
    public static function nextStates(string $current): array
    {
        return self::$transitions[$current] ?? [];
    }
}
```

### Step 2: Use in Booking Model
```php
// app/Models/Booking.php

public function setStatusAttribute($value)
{
    if ($this->exists && !BookingStateMachine::canTransition($this->status, $value)) {
        throw new \InvalidArgumentException(
            "Cannot transition from {$this->status} to {$value}"
        );
    }
    
    $this->attributes['status'] = $value;
    
    // Log status changes
    BookingStatusLog::create([
        'booking_id' => $this->id,
        'from_status' => $this->status,
        'to_status' => $value,
        'changed_by' => auth()->id(),
    ]);
}
```

### Step 3: Update Controller
```php
// app/Http/Controllers/Api/Admin/AdminBookingController.php
// Find: public function updateStatus()

public function updateStatus(Request $request, Booking $booking)
{
    $validated = $request->validate([
        'status' => 'required|in:' . implode(',', BookingStateMachine::nextStates($booking->status)),
    ]);
    
    try {
        $booking->update(['status' => $validated['status']]);
        
        return $this->success($booking, "Booking status updated to {$validated['status']}");
    } catch (\InvalidArgumentException $e) {
        return $this->error($e->getMessage(), 422);
    }
}
```

### Test It
```bash
# Try invalid transition (completed → accepted)
curl -X PATCH "http://localhost/api/v1/bookings/1/status" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -d '{"status": "accepted"}'

# Should error with message about invalid transition
```

---

# ⚠️ PRIORITY 4: Account Approval Enforcement (Issue #U2)

## Impact: CRITICAL - Unauthorized Access
Unapproved users can login

### Step 1: Update Auth Controller
```php
// app/Http/Controllers/Api/AuthController.php
// Find: public function login()

public function login(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    
    $user = User::where('email', $validated['email'])->first();
    
    if (!$user || !Hash::check($validated['password'], $user->password)) {
        return $this->error('Invalid credentials', 401);
    }
    
    // NEW: Check approval status
    if ($user->approval_status === 'pending') {
        return $this->error(
            'Your account is pending approval. You will receive an email when approved.',
            403
        );
    }
    
    if ($user->approval_status === 'rejected') {
        return $this->error(
            'Your account registration was rejected. ' .
            ($user->rejection_reason ? "Reason: {$user->rejection_reason}" : ''),
            403
        );
    }
    
    // NEW: Check if suspended
    if ($user->is_suspended) {
        return $this->error(
            "Your account has been suspended. " .
            ($user->suspension_reason ? "Reason: {$user->suspension_reason}" : ''),
            403
        );
    }
    
    // Check email verification
    if (!$user->hasVerifiedEmail()) {
        return $this->error(
            'Please verify your email before logging in. Check your inbox for verification link.',
            403
        );
    }
    
    // All checks passed - login
    $token = $user->createToken('auth_token')->plainTextToken;
    
    $user->update([
        'last_login_at' => now(),
        'last_login_ip' => $request->ip(),
    ]);
    
    return $this->success([
        'token' => $token,
        'user' => $user->only(['id', 'name', 'email', 'role']),
    ], 'Logged in successfully');
}
```

### Step 2: Create Approval Middleware
```php
// app/Http/Middleware/RequireApproval.php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireApproval
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->approval_status !== 'approved') {
            return response()->json([
                'message' => 'Your account has not been approved'
            ], 403);
        }
        
        return $next($request);
    }
}
```

### Step 3: Apply to Protected Routes
```php
// Update routes/api.php
Route::middleware(['auth:sanctum', 'require_approval'])->group(function () {
    // All photographer, booking, etc endpoints
});
```

### Test It
```bash
# Try to login with unapproved user
curl -X POST http://localhost/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "pending@test.com", "password": "password"}'

# Should return 403 Forbidden
```

---

# ⚠️ PRIORITY 5: Add Critical Database Indexes (Issue #PF1)

## Impact: CRITICAL - Performance Collapse
Queries scanning 50,000+ rows take 2-10 seconds

### Step 1: Create Migration
```bash
php artisan make:migration add_critical_performance_indexes_v2
```

### Step 2: Add Indexes
```php
// database/migrations/2026_02_16_add_critical_performance_indexes_v2.php

Schema::table('photographers', function (Blueprint $table) {
    $table->index(['city_id', 'is_available']);
    $table->index(['user_id']);
    $table->index('is_verified');
});

Schema::table('competition_submissions', function (Blueprint $table) {
    $table->index(['competition_id', 'status']);
    $table->index(['photographer_id', 'status']);
    $table->index('status');
});

Schema::table('event_registrations', function (Blueprint $table) {
    $table->index(['event_id', 'user_id']);
    $table->index('event_id');
});

Schema::table('notifications', function (Blueprint $table) {
    $table->index(['user_id', 'created_at']);
    $table->index(['user_id', 'read_at']);
});

Schema::table('competition_votes', function (Blueprint $table) {
    $table->index(['competition_id', 'submission_id']);
    $table->index(['user_id', 'competition_id']);
});

Schema::table('competition_scores', function (Blueprint $table) {
    $table->index(['competition_id', 'submission_id']);
    $table->index(['judge_id', 'competition_id']);
});
```

### Step 3: Run Migration
```bash
php artisan migrate

# Verify indexes created
php artisan tinker
>>> DB::select("SHOW INDEX FROM photographers;")

// Should show new indexes
```

---

# 🚀 DEPLOYMENT CHECKLIST

## Pre-Deployment (Do These in Order)

- [ ] Apply all 5 migrations in order
- [ ] Test each payment verification scenario
- [ ] Test event capacity with concurrent requests
- [ ] Test booking status transitions
- [ ] Test login with unapproved/rejected users
- [ ] Verify database indexes exist
- [ ] Run full test suite
- [ ] Check performance (queries should be < 1s)
- [ ] Review all error messages for clarity
- [ ] Set up error monitoring (Sentry/Rollbar)

## Deployment Steps

```bash
# 1. Backup database
mysqldump -u root -p"password" photographar_sb > backup_$(date +%s).sql

# 2. Run migrations
php artisan migrate

# 3. Clear cache
php artisan cache:clear
php artisan config:clear

# 4. Restart queue workers (if using)
supervisor restart all

# 5. Test in production
curl https://photographar-sb.com/api/v1/health

# 6. Monitor logs
tail -f storage/logs/laravel.log
```

---

# 📊 SUCCESS METRICS

After deployment, verify:

✅ **Security**:
- [ ] Event payments can only be registered if verified
- [ ] Event doesn't overbook past capacity
- [ ] Booking status transitions are valid
- [ ] Unapproved users cannot login
- [ ] All indexes exist (SHOW INDEX checks)

✅ **Performance**:
- [ ] Competition listing loads in < 500ms
- [ ] Photographer search in < 200ms
- [ ] No N+1 queries in logs
- [ ] Database query count < 10 per request

✅ **Data Integrity**:
- [ ] All bookings have valid status history
- [ ] No orphaned payment records
- [ ] Event registrations never exceed capacity
- [ ] User approval status respected

---

## Next Steps After Phase 1

Once these 5 fixes are complete and tested:

1. Proceed to Phase 2 (6 hours):
   - CORS configuration
   - Document validation
   - Log sanitization
   - Payment token handling

2. Proceed to Phase 3 (4 hours):
   - Judge conflict of interest
   - Score locking
   - Judge validations

3. Proceed to Phase 4 (8 hours):
   - Performance optimization
   - N+1 query fixes
   - Query caching

**Total Time**: ~40 hours for all issues

---

## Need Help?

If any fix fails:
1. Check error logs: `tail -f storage/logs/laravel.log`
2. Run tests: `php artisan test`
3. Verify migrations: `php artisan migrate:status`
4. Check database: `php artisan tinker`
