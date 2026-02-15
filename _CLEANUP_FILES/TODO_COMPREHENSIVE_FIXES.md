# COMPREHENSIVE FIX TODO LIST
**Photographer SB Platform - Production Readiness**

---

## P0 - BLOCKING ISSUES (Must Fix This Week)

### ✅ 1. User Approval System - Add Missing Fields
**Priority:** P0 | **Effort:** S | **Risk:** HIGH

**Files Affected:**
- `database/migrations/*` (new migration)
- `app/Models/User.php`
- `app/Http/Controllers/Api/AuthController.php`

**Steps:**
1. Create migration:
```bash
php artisan make:migration add_approval_fields_to_users_table
```

2. Migration content:
```php
Schema::table('users', function (Blueprint $table) {
    $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('role');
    $table->text('rejection_reason')->nullable()->after('approval_status');
});
```

3. Update User model:
```php
protected $fillable = [
    // ... existing
    'approval_status',
    'rejection_reason',
];

protected $casts = [
    // ... existing
    'approval_status' => 'string',
];
```

4. Update AuthController login check - it's already there, just needs fields to exist

**Test:** Register photographer → Login should fail with "pending approval" message

---

### ✅ 2. Create Settings Table
**Priority:** P0 | **Effort:** S | **Risk:** MEDIUM

**Files:**
- `database/migrations/*` (new)
- `database/seeders/SettingsSeeder.php` (new)

**Migration:**
```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->longText('value')->nullable();
    $table->string('group')->default('general'); // tracking, email, payment, etc.
    $table->timestamps();
});
```

**Seed Data:**
```php
Settings::create(['key' => 'ga4_measurement_id', 'value' => '', 'group' => 'tracking']);
Settings::create(['key' => 'fb_pixel_id', 'value' => '', 'group' => 'tracking']);
Settings::create(['key' => 'platform_commission', 'value' => '10', 'group' => 'payment']);
```

**Test:** `GET /api/v1/admin/settings` should return data

---

### ✅ 3. Fix OAuth Configuration
**Priority:** P0 | **Effort:** S | **Risk:** MEDIUM

**Files:** `.env`, `config/services.php`

**Add to `.env`:**
```
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=${APP_URL}/api/v1/auth/google/callback

GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_REDIRECT_URI=${APP_URL}/api/v1/auth/github/callback
```

**Test:** `GET /api/v1/auth/google/redirect` should return redirect URL (not 500)

---

### ✅ 4. Fix Route Model Binding
**Priority:** P0 | **Effort:** M | **Risk:** LOW

**File:** `app/Providers/RouteServiceProvider.php`

**Add:**
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

**Test:**
- `GET /api/v1/competitions/11` → 200 (was 404)
- `GET /api/v1/judges/10` → 404 (needs proper ID)
- `GET /api/v1/mentors/5` → 404 (needs proper ID)

---

### ✅ 5. Clear Rate Limit Cache
**Priority:** P0 | **Effort:** S | **Risk:** LOW

**Command:**
```bash
php artisan cache:clear
redis-cli FLUSHALL
```

**Verify:**
- `POST /api/v1/auth/reset-password` → Should not 429

---

### ✅ 6. Implement Closed Endpoint Routes
**Priority:** P0 | **Effort:** M | **Risk:** MEDIUM

**Files:**
- `app/Http/Controllers/Api/Admin/JudgeController.php` - exists, update route group
- `app/Http/Controllers/Api/Admin/MentorController.php` - exists, update
- `routes/api.php` - update judges/mentors route group

**Current:**
```php
Route::get('/api/v1/judges', fn() => response()->json(['data' => []]));
```

**Fix:**
```php
// In Api Admin group
Route::apiResource('judges', JudgeController::class); // CRUD routes
Route::apiResource('mentors', MentorController::class);
```

**Test:**
- `GET /api/v1/judges` → 200 with list
- `GET /api/v1/judges/{id}` → 200 or 404 correctly

---

## P1 - MAJOR ISSUES (This Month)

### 7. Fix CompetitionSubmission Model Fillable
**Priority:** P1 | **Effort:** S | **Risk:** LOW

**File:** `app/Models/CompetitionSubmission.php`

**Issue:** Migration added `image_path` but model doesn't include it:

```php
// ADD to $fillable:
protected $fillable = [
    // ...
    'image_path', // Add this
];
```

**Note:** Consider deprecating `image_path` - it's redundant with `image_url`.

---

### 8. Optimize N+1 Query Problems
**Priority:** P1 | **Effort:** M | **Risk:** LOW

**Files to Update:**
- `app/Http/Controllers/Api/Admin/AdminEventApiController.php`
- `app/Http/Controllers/Api/Admin/AdminCompetitionApiController.php`
- `app/Http/Controllers/Api/PhotographerController.php`
- `app/Http/Controllers/Api/BookingController.php`

**Pattern:**
```php
// BEFORE
$events = $query->paginate(20);

// AFTER
$events = $query->with(['organizer.user', 'city'])->paginate(20);
```

**Test:** Run query and check DB queries count (should be ≤ 2 per page load).

---

### 9. Implement Authorization Policies
**Priority:** P1 | **Effort:** L | **Risk:** MEDIUM

**Create Policies:**
```bash
php artisan make:policy PhotographerPolicy --model=Photographer
php artisan make:policy BookingPolicy --model=Booking
php artisan make:policy CompetitionSubmissionPolicy --model=CompetitionSubmission
```

**PhotographerPolicy:**
```php
public function update(User $user, Photographer $photographer): bool {
    return $user->id === $photographer->user_id || $user->isAdmin();
}

public function delete(User $user, Photographer $photographer): bool {
    return $user->isAdmin();
}
```

**Register in AuthServiceProvider:**
```php
protected $policies = [
    Photographer::class => PhotographerPolicy::class,
    Booking::class => BookingPolicy::class,
];
```

**Apply in Controllers:**
```php
$this->authorize('update', $photographer);
$booking->update($validated); // Checks authorization
```

---

### 10. Add System Health Dashboard Endpoint
**Priority:** P1 | **Effort:** M | **Risk:** LOW

**Create Controller:**
```bash
php artisan make:controller Api/Admin/HealthController
```

**Endpoint:**
```php
// GET /api/v1/admin/health
public function check() {
    return response()->json([
        'status' => 'healthy',
        'database' => DB::table('users')->count() > 0 ? 'ok' : 'down',
        'cache' => Cache::get('health_check') ?? 'ok',
        'recent_errors' => ActivityLog::where('type', 'error')->latest()->limit(10)->get(),
        'failed_jobs' => Job::where('failed_at', '!=', null)->count(),
    ]);
}
```

---

### 11. Implement Caching for Dashboard Stats
**Priority:** P1 | **Effort:** M | **Risk:** LOW

**File:** `app/Http/Controllers/Api/AdminController.php`

**Pattern:**
```php
public function dashboard() {
    return Cache::remember('admin_dashboard_stats', 3600, function () {
        return [
            'total_photographers' => Photographer::count(),
            'total_competitions' => Competition::count(),
            'total_revenue' => Transaction::where('status', 'completed')->sum('amount'),
        ];
    });
}
```

**Note:** Clear cache on updates:
```php
// In controller store/update methods
Cache::forget('admin_dashboard_stats');
```

---

### 12. Create Validation FormRequest Classes
**Priority:** P1 | **Effort:** L | **Risk:** LOW

**Create FormRequests:**
```bash
php artisan make:request StoreCityRequest
php artisan make:request StorePhotographerRequest
php artisan make:request StoreCompetitionRequest
```

**Example:**
```php
// app/Http/Requests/StoreCityRequest.php
public function rules(): array {
    return [
        'name' => 'required|string|unique:cities',
        'slug' => 'required|string|unique:cities',
        'division' => 'nullable|string',
    ];
}
```

**Use in Controller:**
```php
public function store(StoreCityRequest $request) {
    $validated = $request->validated();
    City::create($validated);
}
```

---

### 13. Standardize API Response Format
**Priority:** P1 | **Effort:** M | **Risk:** MEDIUM

**Create Response Trait:**
```php
// app/Http/Traits/ApiResponse.php
trait ApiResponse {
    public function success($data = null, $message = 'Success', $code = 200) {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }
    
    public function error($message = 'Error', $code = 400, $errors = null) {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
```

**Use in All Controllers:**
```php
class PhotoographerController extends Controller {
    use ApiResponse;
    
    public function store(Request $request) {
        // ... validation
        return $this->success($photographer, 'Photographer created', 201);
    }
}
```

---

### 14. Add N+1 Detection Package
**Priority:** P1 | **Effort:** S | **Risk:** LOW

**Install:**
```bash
composer require barryvdh/laravel-debugbar
```

**Enable in dev:**
```
DEBUGBAR_ENABLED=true
```

**Benefit:** See query count in response headers.

---

### 15. Seed Bangladesh Geographic Data
**Priority:** P1 | **Effort:** M | **Risk:** LOW

**Create Seeder:**
```bash
php artisan make:seeder BangladeshCitiesSeeder
```

**Cities Needed:**
```php
$cities = [
    // Dhaka Division
    ['name' => 'Dhaka', 'division' => 'Dhaka', 'slug' => 'dhaka'],
    ['name' => 'Narayanganj', 'division' => 'Dhaka', 'slug' => 'narayanganj'],
    ['name' => 'Gazipur', 'division' => 'Dhaka', 'slug' => 'gazipur'],
    // ... 61 more districts
];

foreach ($cities as $city) {
    City::create($city);
}
```

**Register in DatabaseSeeder:**
```php
public function run() {
    $this->call([
        BangladeshCitiesSeeder::class,
    ]);
}
```

---

### 16. Seed Default Photography Categories
**Priority:** P1 | **Effort:** S | **Risk:** LOW

**Seeder Content:**
```php
$categories = [
    ['name' => 'Wedding', 'slug' => 'wedding'],
    ['name' => 'Portrait', 'slug' => 'portrait'],
    ['name' => 'Event', 'slug' => 'event'],
    ['name' => 'Landscape', 'slug' => 'landscape'],
    ['name' => 'Product', 'slug' => 'product'],
    ['name' => 'Nature', 'slug' => 'nature'],
    ['name' => 'Corporate', 'slug' => 'corporate'],
    ['name' => 'Lifestyle', 'slug' => 'lifestyle'],
];

foreach ($categories as $cat) {
    Category::create($cat);
}
```

---

### 17. Migrate Sponsor Fields
**Priority:** P1 | **Effort:** S | **Risk:** LOW

**Issue:** Schema has `logo`, `website` but code expects `logo_url`, `website_url`.

**Migration:**
```php
Schema::table('sponsors', function (Blueprint $table) {
    // Rename for consistency
    $table->renameColumn('logo', 'logo_url');
    $table->renameColumn('website', 'website_url');
});
```

**Update Model:**
```php
protected $fillable = [
    'name', 'slug', 'logo_url', 'website_url', // Updated
    'description', 'status', 'display_order',
];
```

---

### 18. Add Certificate ID Generation
**Priority:** P1 | **Effort:** M | **Risk:** LOW

**File:** `app/Services/CertificateService.php`

**Issue:** Certificate ID remains NULL.

**Fix:**
```php
public function generateCertificate($submission) {
    $certificateId = 'CERT-' . strtoupper(uniqid());
    
    $submission->update([
        'certificate_id' => $certificateId,
        'certificate_url' => $this->generateUrl($certificateId),
        'certificate_generated_at' => now(),
    ]);
}
```

---

## P2 - NICE-TO-HAVE (Polish)

### 19. Add OG Meta Tags
**Priority:** P2 | **Effort:** M | **Risk:** LOW

**Frontend responsibility** - but backend should provide:

**API Enhancement:**
```php
// GET /api/v1/photographers/{photographer}
return response()->json([
    'data' => $photographer,
    'meta' => [
        'og_title' => $photographer->user->name,
        'og_description' => $photographer->bio,
        'og_image' => $photographer->profile_photo_url,
        'share_url' => route('photographer.profile.public', ['username' => $photographer->user->username]),
    ],
]);
```

---

### 20. Implement Schema Markup
**Priority:** P2 | **Effort:** M | **Risk:** LOW

**JSON-LD for Photographer:**
```php
{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": "Photographer Name",
  "image": "profile.jpg",
  "description": "bio",
  "jobTitle": "Photography Category",
  "location": {"@type": "Place", "name": "City"},
}
```

**JSON-LD for Event:**
```php
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "Event Title",
  "description": "...",
  "startDate": "2026-02-15",
  "location": {"@type": "Place", "name": "Venue"},
}
```

---

### 21. Create robots.txt
**Priority:** P2 | **Effort:** S | **Risk:** LOW

**File:** `public/robots.txt`

```
User-agent: *
Allow: /
Disallow: /api/
Disallow: /admin/
Sitemap: https://photographar.bd/sitemap.xml
```

---

### 22. Add Profile View Analytics
**Priority:** P2 | **Effort:** M | **Risk:** LOW

**Track Profile Views:**
```php
// PublicPhotographerController@showByUsername
VisitorLog::create([
    'photographer_id' => $photographer->id,
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);

$photographer->increment('profile_view_count');
```

---

### 23. Implement Review Moderation
**Priority:** P2 | **Effort:** M | **Risk:** LOW

**Workflow:**
```php
// Review created with status = 'pending'
// Admin reviews and either:
Review::update(['status' => 'published']); // or
Review::update(['status' => 'hidden', 'moderation_notes' => '...']);
```

---

### 24. Add Advanced Photographer Search
**Priority:** P2 | **Effort:** L | **Risk:** LOW

**Filters:**
- `?category=wedding`
- `?city_id=1`
- `?min_rating=4`
- `?price_min=5000&price_max=50000`
- `?availability=available`

```php
public function search(Request $request) {
    $query = Photographer::query();
    
    if ($request->has('category')) {
        $query->whereHas('categories', fn($q) => $q->where('slug', $request->category));
    }
    if ($request->has('city_id')) {
        $query->where('city_id', $request->city_id);
    }
    if ($request->has('min_rating')) {
        $query->where('average_rating', '>=', $request->min_rating);
    }
    
    return $query->paginate();
}
```

---

### 25. Create Photographer Onboarding Checklist
**Priority:** P2 | **Effort:** M | **Risk:** LOW

**Checklist Items:**
- Upload profile photo
- Write bio
- Add categories
- Add city
- Create first package
- Upload portfolio (albums)
- Add social links
- Request verification

**API Endpoint:**
```php
// GET /api/v1/photographer/onboarding-progress
{
    'completion_percentage': 45,
    'completed_items': ['profile_photo', 'bio'],
    'pending_items': ['first_package', 'portfolio'],
}
```

---

## Testing Checklist

After all fixes, verify:

- [ ] `php artisan test` - all tests pass
- [ ] `php artisan route:list` - no errors
- [ ] `php artisan migrate:status` - all migrated
- [ ] Admin can approve/reject photographers
- [ ] Photographers see pending approval message
- [ ] Settings UI works in admin
- [ ] All routes return proper HTTP status
- [ ] No 404 errors for valid resources
- [ ] Rate limiting works correctly
- [ ] OAuth login flow works
- [ ] Mobile responsive (manual test)
- [ ] Sitemap updates with new data
- [ ] Bangladesh cities/categories seeded

---

## Timeline Estimate

| Phase | Duration | Owner |
|-------|----------|-------|
| P0 Blockers | 2-3 days | Backend Lead |
| P1 Major | 1 week | Full Team |
| P2 Polish | 1 week | Frontend + UX |
| Testing | 3-4 days | QA |
| **Total** | **3-4 weeks** | |

---

**Generated:** 2026-02-03
**Review Date:** After P0 completion
