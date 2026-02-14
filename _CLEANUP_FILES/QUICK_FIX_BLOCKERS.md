# QUICK START - FIX THESE BLOCKERS NOW
**Photographer SB - P0 Blocker Quick Fix Guide (3 Hours)**

---

## 🚨 What's Broken Right Now

| Priority | Issue | Fix Time | Status |
|----------|-------|----------|--------|
| 🔴 P0 | Approval system missing | 30 min | Migration ready |
| 🔴 P0 | Settings table missing | 30 min | Migration ready |
| 🟠 P1 | Route model binding broken | 15 min | Code ready |
| 🟠 P1 | OAuth not configured | 30 min | Docs ready |
| 🟡 P2 | Bangladesh data missing | 15 min | Seeder ready |

---

## ⚡ EXPRESS FIX (Copy-Paste Ready)

### Step 1: Apply Migrations (2 min)
```bash
cd c:\xampp\htdocs\Photographar\ SB
php artisan migrate
```

**What it does:**
- Adds `approval_status` to users table
- Creates `settings` table
- Adds Bangladesh geographic fields

✅ **Check:** 
```bash
php artisan migrate:status
# Look for both 2026_02_03 migrations as "yes"
```

---

### Step 2: Update User Model (5 min)

**File:** `app/Models/User.php`

**Find this:**
```php
protected $fillable = [
    'uuid', 'name', 'username', 'email', 'phone', 'password', 'role',
    // ... more
];
```

**Replace with:**
```php
protected $fillable = [
    'uuid', 'name', 'username', 'email', 'phone', 'password', 'role',
    'approval_status', 'rejection_reason', 'approved_at', 'approved_by_admin_id',
    // ... rest
];

protected $casts = [
    'approval_status' => 'string',
    'approved_at' => 'datetime',
    // ... rest
];

// ADD these methods at bottom:
public function isApproved(): bool {
    return $this->approval_status === 'approved';
}

public function isPendingApproval(): bool {
    return $this->approval_status === 'pending';
}

public function isRejected(): bool {
    return $this->approval_status === 'rejected';
}
```

---

### Step 3: Fix AuthController (5 min)

**File:** `app/Http/Controllers/Api/AuthController.php`

**Find (around line 85):**
```php
if ($user->approval_status === 'pending') {
    // existing code
}
```

**This already works!** No change needed (field now exists)

---

### Step 4: Add Route Model Binding (10 min)

**File:** `app/Providers/RouteServiceProvider.php`

**Find:**
```php
public function boot(): void
{
    // existing code
}
```

**Add this at start of boot():**
```php
Route::model('competition', \App\Models\Competition::class);
Route::model('judge', \App\Models\Judge::class);
Route::model('mentor', \App\Models\Mentor::class);
Route::model('event', \App\Models\Event::class);
Route::model('photographer', \App\Models\Photographer::class);
```

**What it does:** Fixes the 404 errors on Competition, Judge, Mentor, Event endpoints

✅ **Check:**
```bash
curl http://localhost:8000/api/v1/competitions/1
# Should return 200 (not 404)
```

---

### Step 5: Seed Settings (5 min)
```bash
php artisan db:seed --class=PlatformSettingsSeeder
```

✅ **Check:**
```bash
php artisan tinker
App\Models\Setting::count()
# Should show 30+
```

---

### Step 6: Seed Bangladesh Data (5 min)
```bash
php artisan db:seed --class=BangladeshCitiesSeeder
```

✅ **Check:**
```bash
php artisan tinker
App\Models\City::count()
# Should show 60+
```

---

### Step 7: Clear Cache (2 min)
```bash
php artisan cache:clear
php artisan route:clear
php artisan config:clear
```

---

### Step 8: Test Everything (20 min)

**Test 1: Approval System**
```bash
# Try to login before approval
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "photographer@example.com",
    "password": "password"
  }'
# Should return 403: "Your account is pending approval"
```

**Test 2: Settings**
```bash
curl http://localhost:8000/api/v1/settings
# Should return settings with platform_name, currency, etc.
```

**Test 3: Route Binding**
```bash
curl http://localhost:8000/api/v1/competitions/1
# Should return 200 (or 404 if competition doesn't exist, but not route error)
```

**Test 4: Bangladesh Data**
```bash
curl http://localhost:8000/api/v1/cities
# Should return 60+ Bangladeshi cities
```

---

## 🎯 What This Fixes

After these 8 steps:

| Before | After |
|--------|-------|
| ❌ Route 404 for judges | ✅ Returns data or 404 properly |
| ❌ Settings feature broken | ✅ Admin can manage settings |
| ❌ Non-approved users can login | ✅ Blocked until approved |
| ❌ No Bangladesh cities | ✅ All 60+ districts available |
| ❌ OAuth returns 500 | ✅ Waits for .env credentials |

---

## ⏱️ Time Estimate

| Step | Time |
|------|------|
| 1. Migrate | 2 min ⚡ |
| 2. Update User | 5 min |
| 3. AuthController | 0 min (already works) |
| 4. Route Binding | 10 min |
| 5. Seed Settings | 5 min |
| 6. Seed Cities | 5 min |
| 7. Clear Cache | 2 min |
| 8. Test | 20 min |
| **TOTAL** | **~50 minutes** ✅ |

---

## 🔧 If Something Goes Wrong

**Issue: Migrate fails**
```bash
# Rollback and try again
php artisan migrate:rollback
php artisan migrate
```

**Issue: Seeder fails**
```bash
# Check if table exists
php artisan tinker
Schema::hasTable('settings')

# If no, migrate again
php artisan migrate
```

**Issue: Still getting 404**
```bash
# Clear route cache
php artisan route:clear

# Verify RouteServiceProvider was updated
grep -n "Route::model" app/Providers/RouteServiceProvider.php
# Should show 5 model bindings
```

---

## 📋 Do This For OAuth (When Ready)

**File:** `.env`

```env
GOOGLE_CLIENT_ID=your_google_id_here
GOOGLE_CLIENT_SECRET=your_google_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/api/v1/auth/google/callback
```

**Get them from:**
1. [Google Cloud Console](https://console.cloud.google.com/) → OAuth 2.0 → Create Credentials
2. Add Authorized Redirect URI: `http://localhost:8000/api/v1/auth/google/callback`
3. Copy Client ID and Secret to .env

**Then test:**
```bash
curl http://localhost:8000/api/v1/auth/google/redirect
# Should return 200 with redirect URL (not 500)
```

---

## ✅ Done? Check This

After everything is done:

```bash
# 1. All migrations applied
php artisan migrate:status

# 2. Settings exist
php artisan tinker
App\Models\Setting::where('key', 'platform_name')->first()
# Should return Setting object with "Photographar" value

# 3. Cities exist
App\Models\City::where('slug', 'dhaka')->first()
# Should return Dhaka city

# 4. Route binding works
exit
curl http://localhost:8000/api/v1/judges
# Should return JSON (not route not found error)

# 5. Approval system active
php artisan tinker
$user = App\Models\User::find(1);
$user->isPendingApproval() # or isApproved() or isRejected()
```

---

## 📞 Need Help?

**Reference Files:**
- Full details: `P0_IMPLEMENTATION_GUIDE.md`
- All issues: `COMPREHENSIVE_AUDIT_REPORT_2026_02_03.md`
- All tasks: `TODO_COMPREHENSIVE_FIXES.md`

**Commands Quick Reference:**
```bash
# Run everything at once
php artisan migrate
php artisan db:seed --class=PlatformSettingsSeeder
php artisan db:seed --class=BangladeshCitiesSeeder
php artisan cache:clear
php artisan route:clear
php artisan config:clear

# Verify
php artisan tinker
App\Models\Setting::count()
App\Models\City::count()
```

---

**Status: READY TO FIX** 🚀  
**Time Investment: 50 minutes**  
**Result: Production-grade approval system + settings management**
