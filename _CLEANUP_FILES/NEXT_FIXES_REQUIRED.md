# NEXT CRITICAL FIXES REQUIRED
**Priority Order for Remaining Issues**

---

## 🔴 P0 BLOCKERS - MUST FIX NEXT

### TICKET #1: Implement Competition Winner Calculation
**Priority:** P0 - BLOCKER  
**File:** `app/Http/Controllers/Api/CompetitionController.php` (line ~340)  
**Status:** Currently STUB - returns dummy data only  
**Time Estimate:** 4 hours  

**Current Code (BROKEN):**
```php
public function calculateWinners(Request $request, Competition $competition, WinnerCalculationService $winnerService)
{
    // TODO: Implement winner calculation
    return response()->json(['stub' => true]);
}
```

**What Needs to Happen:**
1. Get all approved submissions for competition
2. Calculate weighted score: (public_votes * 0.4) + (judge_scores * 0.6)
3. Identify top 3 winners
4. Assign medals (gold=1st, silver=2nd, bronze=3rd)
5. Update `competition_submissions` table with:
   - `prize_position` (1, 2, 3, or NULL)
   - `winner_announced_at` (timestamp)
6. Create activity log entry
7. Send email notifications to winners

**Database Schema Already Exists:**
- Table: `competition_submissions`
- Columns: `prize_position`, `winner_announced_at` ✅
- Relationships: `hasMany votes`, `hasMany scores` ✅

**API Endpoint:**
```
POST /api/v1/admin/competitions/{competition}/calculate-winners
Body: {
  "vote_weight": 0.4,
  "judge_weight": 0.6,
  "number_of_winners": 3
}
```

**Test Case:**
1. Create competition with 10 submissions
2. Add 20 votes to each (vary amounts)
3. Add 5 judges with scores 1-5
4. Call calculateWinners endpoint
5. Verify top 3 have prize_position set
6. Verify others have NULL

---

### TICKET #2: Generate Digital Certificates
**Priority:** P0 - BLOCKER  
**File:** `app/Http/Controllers/Api/CompetitionController.php` (line ~380)  
**Status:** Currently STUB  
**Time Estimate:** 5 hours  

**Required Package:**
```bash
composer require barryvdh/laravel-dompdf
```

**What Needs to Happen:**
1. After winners calculated, generate PDF certificates
2. Certificates should include:
   - Photographer name
   - Competition name
   - Position (1st Place / Gold Medal)
   - Certificate number (unique)
   - Date
   - Digitally signed/sealed
   - Logo/branding
3. Store PDF in: `storage/certificates/{year}/{competition_id}/{submission_id}.pdf`
4. Update `competition_submissions.certificate_path` with file path
5. Create download endpoint

**Certificate Template Needed:**
```
┌─────────────────────────────────┐
│   Photographer SB               │
│      CERTIFICATE OF             │
│        ACHIEVEMENT              │
├─────────────────────────────────┤
│ This is to certify that          │
│ [PHOTOGRAPHER NAME]             │
│                                 │
│ Won 1st Place in:              │
│ [COMPETITION NAME]             │
│                                 │
│ Date: [DATE]                    │
│ Certificate #: [CERT_ID]        │
│                                 │
│ 🏆 GOLD MEDAL 🏆               │
└─────────────────────────────────┘
```

**API Endpoints Needed:**
```
POST   /api/v1/admin/competitions/{id}/generate-certificates
       (Generate all winner certificates)

POST   /api/v1/admin/competitions/{id}/generate-certificate
       (Generate single certificate)

GET    /api/v1/competitions/{id}/submissions/{id}/certificate
       (Download certificate)
```

**Test Case:**
1. Mark submission as winner (prize_position=1)
2. POST /generate-certificates
3. Verify PDF file created in storage
4. GET /certificate endpoint and download
5. Verify PDF readable and contains correct data

---

## 🟠 P1 HIGH PRIORITY - SHOULD FIX NEXT

### TICKET #3: Fix CompetitionScore Model Relationships
**Priority:** P1  
**File:** `app/Models/CompetitionScore.php`  
**Status:** Missing FK relationships  
**Time Estimate:** 1.5 hours  

**What Needs to Happen:**
```php
// Add these relationships to CompetitionScore model:

public function competition()
{
    return $this->belongsTo(Competition::class);
}

public function submission()
{
    return $this->belongsTo(CompetitionSubmission::class);
}

public function judge()
{
    return $this->belongsTo(User::class, 'judge_id');
}

public function criterion()
{
    return $this->belongsTo(ScoringCriterion::class);
}
```

**Verify:**
```php
$score = CompetitionScore::with('judge', 'submission', 'competition')->first();
echo $score->judge->name;         // Should work
echo $score->submission->title;   // Should work
```

---

### TICKET #4: Create Email Templates & Mailables
**Priority:** P1  
**Files:** `app/Mail/*.php`, `resources/views/emails/*.blade.php`  
**Status:** Not started  
**Time Estimate:** 4 hours  

**Mailables Needed:**

1. **VerifyEmailMailable** - Email verification link
   ```php
   // app/Mail/VerifyEmailMailable.php
   class VerifyEmailMailable extends Mailable {
       public function envelope() {
           return new Envelope(
               subject: 'Verify Your Email Address',
           );
       }
       
       public function content() {
           return new Content(
               view: 'emails.verify-email',
           );
       }
   }
   ```

2. **BookingConfirmedMailable** - Booking confirmation
3. **QuoteReceivedMailable** - Quote from photographer
4. **PaymentConfirmedMailable** - Payment receipt
5. **CompetitionWinnerMailable** - Winner notification

**Template File Example:**
```blade
<!-- resources/views/emails/verify-email.blade.php -->
<h1>Welcome, {{ $user->name }}!</h1>
<p>Please verify your email address to continue:</p>
<a href="{{ $verificationUrl }}">Verify Email</a>
<p>Link expires in 24 hours</p>
```

**Usage in Controller:**
```php
Mail::to($user->email)->send(new VerifyEmailMailable($user, $verificationUrl));
```

---

### TICKET #5: Integrate Phone OTP Verification
**Priority:** P1  
**File:** `app/Http/Controllers/Api/AuthController.php`  
**Status:** Method exists but incomplete  
**Time Estimate:** 4 hours  

**Required Package:**
```bash
composer require twilio/sdk
```

**What Needs to Happen:**
1. Send OTP to phone (SMS via Twilio)
2. Store OTP in database with expiry (5 minutes)
3. Verify OTP when user enters it
4. Mark `phone_verified` in users table

**Endpoints Needed:**
```
POST /api/v1/auth/send-phone-otp
     Body: { "phone": "+8801700000000" }

POST /api/v1/auth/verify-phone-otp
     Body: { "phone": "+8801700000000", "otp": "123456" }
```

**Implementation:**
```php
// In AuthController:

public function sendPhoneOtp(Request $request) {
    $phone = $request->validate(['phone' => 'required|regex:/^\+88\d{10}$/']);
    
    // Generate 6-digit OTP
    $otp = rand(100000, 999999);
    
    // Store OTP in cache (5 min)
    Cache::put("phone_otp_{$phone}", $otp, 300);
    
    // Send via Twilio
    Twilio::message($phone, "Your OTP: $otp");
    
    return response()->json(['message' => 'OTP sent']);
}

public function verifyPhoneOtp(Request $request) {
    $data = $request->validate([
        'phone' => 'required',
        'otp' => 'required|numeric|digits:6'
    ]);
    
    $storedOtp = Cache::get("phone_otp_{$data['phone']}");
    
    if ($storedOtp !== (int)$data['otp']) {
        return response()->json(['error' => 'Invalid OTP'], 422);
    }
    
    // Mark phone verified
    auth()->user()->update(['phone_verified_at' => now()]);
    
    return response()->json(['message' => 'Phone verified']);
}
```

---

## 📋 DEPLOYMENT CHECKLIST

Before deploying these fixes, verify:

- [ ] All PHP syntax is valid
- [ ] All migrations pass
- [ ] All new routes compile
- [ ] All Mailable classes can be instantiated
- [ ] All dependencies are installed
- [ ] Database tests pass
- [ ] API tests pass (if test suite exists)
- [ ] No sensitive data in config
- [ ] All error messages are user-friendly

---

## HOW TO PROCEED

**Option 1: Implement Now (Recommended)**
```bash
# 1. Implement winner calculation
vim app/Http/Controllers/Api/CompetitionController.php

# 2. Add certificate generation
composer require barryvdh/laravel-dompdf
vim app/Http/Controllers/Api/CompetitionController.php

# 3. Create email templates
mkdir -p resources/views/emails
# Create template files

# 4. Create Mailable classes
php artisan make:mail VerifyEmailMailable
# Implement each mailable

# 5. Add phone OTP integration
composer require twilio/sdk
# Add to AuthController

# 6. Test everything
php artisan migrate
php artisan serve
```

**Option 2: Use AI Assistant (Recommended for Speed)**
- Provide this checklist to AI
- Ask to implement all remaining P0/P1 items
- AI will generate production-ready code
- You review and deploy

---

## SUCCESS CRITERIA

After implementing all remaining fixes:

✅ Admin dashboard shows real data  
✅ Competitions have working winner calculation  
✅ Winners receive digital certificates (PDF)  
✅ Email notifications sent (verification, booking, payments, winners)  
✅ Phone verification working (SMS OTP)  
✅ Photo upload validated (MIME types, sizes)  
✅ All pagination has limits (prevent DoS)  
✅ Payment refunds working  
✅ Admin routes protected  
✅ Rate limiting on sensitive endpoints  

**Overall Quality Score:** Will reach 85+/100  
**Production Ready:** YES  

---

**Last Updated:** January 31, 2026  
**Fixes Completed:** 9/18  
**Remaining:** 9 fixes  
**Estimated Time:** 18-20 hours  

