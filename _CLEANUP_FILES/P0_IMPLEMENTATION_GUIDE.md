# P0 BLOCKER IMPLEMENTATION GUIDE
**Photographer SB - Critical Path to Production**

---

## Step 1: Apply Approval System Migration

**File:** `database/migrations/2026_02_03_000000_add_approval_system_to_users.php`
**Status:** ✅ Ready (created)

**Execute:**
```bash
php artisan migrate
```

**Verify:**
```bash
php artisan migrate:status
```

**Check Database:**
```sql
DESCRIBE users;
-- Look for: approval_status, rejection_reason, approved_at, approved_by_admin_id
```

---

## Step 2: Create Settings Table

**File:** `database/migrations/2026_02_03_000001_create_settings_table.php`
**Status:** ✅ Ready (created)

**Execute:**
```bash
php artisan migrate
```

**Verify:**
```bash
SELECT * FROM information_schema.TABLES WHERE TABLE_NAME = 'settings';
```

---

## Step 3: Seed Initial Settings

**File:** `database/seeders/PlatformSettingsSeeder.php`
**Status:** ✅ Ready (created)

**Execute:**
```bash
php artisan db:seed --class=PlatformSettingsSeeder
```

**Verify:**
```bash
php artisan tinker
>>> App\Models\Setting::count()
# Should show 30+
```

---

## Step 4: Update User Model

**File:** `app/Models/User.php`

**Add to fillable array:**
```php
protected $fillable = [
    // ... existing fields ...
    'approval_status',
    'rejection_reason',
    'approved_at',
    'approved_by_admin_id',
];

protected $casts = [
    // ... existing casts ...
    'approval_status' => 'string',
    'approved_at' => 'datetime',
];
```

**Add helper methods:**
```php
public function isApproved(): bool {
    return $this->approval_status === 'approved';
}

public function isPendingApproval(): bool {
    return $this->approval_status === 'pending';
}

public function isRejected(): bool {
    return $this->approval_status === 'rejected';
}

public function approveAsAdmin(User $admin, $notes = null): void {
    $this->update([
        'approval_status' => 'approved',
        'approved_at' => now(),
        'approved_by_admin_id' => $admin->id,
        'rejection_reason' => null,
    ]);
}

public function rejectAsAdmin(User $admin, $reason): void {
    $this->update([
        'approval_status' => 'rejected',
        'rejection_reason' => $reason,
    ]);
}
```

---

## Step 5: Update AuthController

**File:** `app/Http/Controllers/Api/AuthController.php`

**Login method (lines ~85-98):**
```php
// BEFORE: Hard-coded check on non-existent field
if ($user->approval_status === 'pending') {
    return response()->json([
        'message' => 'Your account is pending approval. Please wait for admin verification.',
    ], 403);
}

if ($user->approval_status === 'rejected') {
    return response()->json([
        'message' => 'Your account has been rejected. Reason: ' . $user->rejection_reason,
    ], 403);
}

// AFTER: Use new helper methods
if ($user->isPendingApproval()) {
    return response()->json([
        'message' => 'Your account is pending approval. Please wait for admin verification.',
    ], 403);
}

if ($user->isRejected()) {
    return response()->json([
        'message' => 'Your account has been rejected. Reason: ' . $user->rejection_reason,
        'contact_support' => 'support@photographar.bd',
    ], 403);
}

if (!$user->isApproved() && $user->role !== 'admin' && $user->role !== 'super_admin') {
    return response()->json([
        'message' => 'Your account status is invalid. Please contact support.',
    ], 403);
}
```

---

## Step 6: Create Admin Approval Controller

**File:** `app/Http/Controllers/Api/Admin/UserApprovalController.php`
**Status:** Create new

```php
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserApprovalController extends Controller
{
    /**
     * GET /api/v1/admin/users/pending-approval
     * List photographers awaiting approval
     */
    public function pendingApproval()
    {
        $users = User::where('approval_status', 'pending')
                    ->where('role', 'photographer')
                    ->with('photographer')
                    ->paginate();

        return response()->json([
            'status' => 'success',
            'data' => $users,
        ]);
    }

    /**
     * POST /api/v1/admin/users/{user}/approve
     * Approve a pending user
     */
    public function approve(User $user, Request $request)
    {
        $this->authorize('isAdmin');

        if ($user->isApproved()) {
            return response()->json([
                'message' => 'User is already approved',
            ], 409);
        }

        $user->approveAsAdmin(auth()->user(), $request->input('notes'));

        return response()->json([
            'status' => 'success',
            'message' => 'User approved successfully',
            'data' => $user,
        ]);
    }

    /**
     * POST /api/v1/admin/users/{user}/reject
     * Reject a pending user
     */
    public function reject(User $user, Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:255',
        ]);

        $user->rejectAsAdmin(auth()->user(), $validated['reason']);

        // Notify user of rejection
        // TODO: Add email notification

        return response()->json([
            'status' => 'success',
            'message' => 'User rejected successfully',
            'data' => $user,
        ]);
    }
}
```

**Add routes in `routes/api.php`:**
```php
Route::middleware(['api', 'auth:sanctum', 'CheckRole:admin,super_admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('users/pending-approval', [UserApprovalController::class, 'pendingApproval']);
        Route::post('users/{user}/approve', [UserApprovalController::class, 'approve']);
        Route::post('users/{user}/reject', [UserApprovalController::class, 'reject']);
    });
```

---

## Step 7: Create Settings Model (if not exists)

**File:** `app/Models/Setting.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['key', 'value', 'group', 'data_type', 'description', 'is_public'];

    public static function get($key, $default = null) {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value) {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getPublicSettings() {
        return self::where('is_public', true)->get()->pluck('value', 'key');
    }
}
```

---

## Step 8: Add Settings API Endpoint

**File:** `app/Http/Controllers/Api/SettingsController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * GET /api/v1/settings
     * Get public settings (for frontend)
     */
    public function getPublic()
    {
        $settings = Setting::getPublicSettings();

        return response()->json([
            'status' => 'success',
            'data' => $settings,
        ]);
    }

    /**
     * GET /api/v1/admin/settings
     * Get all settings (admin only)
     */
    public function getAllForAdmin()
    {
        $this->authorize('isAdmin');

        $settings = Setting::all()->groupBy('group');

        return response()->json([
            'status' => 'success',
            'data' => $settings,
        ]);
    }

    /**
     * POST /api/v1/admin/settings/{key}
     * Update a setting (admin only)
     */
    public function update($key)
    {
        $this->authorize('isAdmin');

        $validated = request()->validate([
            'value' => 'required',
        ]);

        Setting::set($key, $validated['value']);

        return response()->json([
            'status' => 'success',
            'message' => "Setting '{$key}' updated",
        ]);
    }
}
```

**Add routes:**
```php
// Public
Route::get('settings', [SettingsController::class, 'getPublic']);

// Admin
Route::middleware(['api', 'auth:sanctum', 'CheckRole:admin,super_admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('settings', [SettingsController::class, 'getAllForAdmin']);
        Route::post('settings/{key}', [SettingsController::class, 'update']);
    });
```

---

## Step 9: Fix Route Model Binding

**File:** `app/Providers/RouteServiceProvider.php`

**In boot() method:**
```php
public function boot(): void
{
    Route::model('competition', Competition::class);
    Route::model('judge', Judge::class);
    Route::model('mentor', Mentor::class);
    Route::model('event', Event::class);
    Route::model('photographer', Photographer::class);
}
```

---

## Step 10: Fix OAuth Configuration

**File:** `.env`

**Add:**
```env
# Google OAuth
GOOGLE_CLIENT_ID=YOUR_GOOGLE_CLIENT_ID
GOOGLE_CLIENT_SECRET=YOUR_GOOGLE_CLIENT_SECRET
GOOGLE_REDIRECT_URI=${APP_URL}/api/v1/auth/google/callback

# GitHub OAuth
GITHUB_CLIENT_ID=YOUR_GITHUB_CLIENT_ID
GITHUB_CLIENT_SECRET=YOUR_GITHUB_CLIENT_SECRET
GITHUB_REDIRECT_URI=${APP_URL}/api/v1/auth/github/callback
```

**Get OAuth keys from:**
- [Google Cloud Console](https://console.cloud.google.com/)
- [GitHub Developer Settings](https://github.com/settings/developers)

---

## Step 11: Test All P0 Fixes

**Test 1: Approval System**
```bash
# Register new photographer
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Photographer",
    "email": "test@example.com",
    "password": "Password123!"
  }'

# Try to login (should fail with pending message)
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "Password123!"
  }'
# Expected: 403 "Your account is pending approval"

# Approve as admin
curl -X POST http://localhost:8000/api/v1/admin/users/34/approve \
  -H "Authorization: Bearer ADMIN_TOKEN"

# Try login again (should succeed)
```

**Test 2: Settings**
```bash
# Get public settings
curl http://localhost:8000/api/v1/settings

# Should return:
# {
#   "platform_name": "Photographar",
#   "platform_currency": "BDT",
#   ...
# }
```

**Test 3: Route Binding**
```bash
# Get competition by ID (should now work)
curl http://localhost:8000/api/v1/competitions/1

# Should return 200 (not 404)
```

**Test 4: OAuth**
```bash
# Try Google redirect
curl http://localhost:8000/api/v1/auth/google/redirect

# Should return 200 with redirect URL (not 500)
```

---

## Step 12: Seed Bangladesh Data

**Execute:**
```bash
php artisan db:seed --class=BangladeshCitiesSeeder
php artisan db:seed --class=PhotographyCategoriesSeeder
```

**Verify:**
```bash
php artisan tinker
>>> App\Models\City::count()
# Should be 60+
>>> App\Models\Category::count()
# Should be 15+
```

---

## Timeline

| Step | Task | Time | Owner |
|------|------|------|-------|
| 1-2 | Apply migrations | 15 min | Backend |
| 3 | Update models | 20 min | Backend |
| 4-5 | Update AuthController | 15 min | Backend |
| 6 | Create approval controller | 30 min | Backend |
| 7-8 | Settings system | 30 min | Backend |
| 9 | Route model binding | 10 min | Backend |
| 10 | OAuth config | 10 min | DevOps |
| 11 | Testing | 30 min | QA |
| 12 | Data seeding | 10 min | Backend |
| **Total** | **P0 Completion** | **170 min (~3 hours)** | |

---

## Deployment Checklist

- [ ] All migrations executed on staging
- [ ] Settings table verified in DB
- [ ] Approval system tested
- [ ] Route model binding works
- [ ] Settings API returns data
- [ ] OAuth keys configured
- [ ] Bangladesh cities seeded
- [ ] Photography categories seeded
- [ ] All tests pass: `php artisan test`
- [ ] No route 404 errors
- [ ] Admin approval flow works end-to-end
- [ ] Login blocks unapproved photographers

---

**Status:** Ready to implement
**Estimated Completion:** Feb 3-4, 2026
**Success Metrics:** All 10 steps completed, zero blocker routes remaining
