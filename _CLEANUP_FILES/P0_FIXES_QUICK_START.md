# ⚡ QUICK START: P0 FIXES (Copy-Paste Ready)

## Fix #1: CompetitionScore Relationships (30 min)

```php
// FILE: app/Models/CompetitionScore.php
// LOCATION: Add these methods after existing code

public function competition(): BelongsTo
{
    return $this->belongsTo(Competition::class);
}

public function submission(): BelongsTo
{
    return $this->belongsTo(CompetitionSubmission::class);
}

public function judge(): BelongsTo
{
    return $this->belongsTo(User::class, 'judge_id');
}

public function criterion(): BelongsTo
{
    return $this->belongsTo(ScoringCriterion::class, 'criterion_id');
}
```

**Test it:**
```bash
php artisan tinker
>>> $score = CompetitionScore::with('judge', 'submission', 'competition')->first();
>>> $score->judge->name
>>> $score->submission->title
```

---

## Fix #2: Image Processing Fallback (1.5 hours)

```php
// FILE: app/Http/Controllers/Api/CompetitionSubmissionController.php
// LOCATION: In the store() method, wrap image processing in try-catch

try {
    // Check if GD available
    if (!extension_loaded('gd') && !extension_loaded('imagick')) {
        return $this->storeOriginalImage($file);
    }
    
    // Your existing image processing here
    $imagePath = $this->generateThumbnail($file);
    
} catch (\Exception $e) {
    Log::error('Image processing failed: ' . $e->getMessage());
    
    // Fall back to storing without thumbnail
    $imagePath = Storage::disk('public')->put('submissions', $file);
}
```

**Add these helper methods:**

```php
private function storeOriginalImage($file)
{
    return Storage::disk('public')->put('submissions', $file);
}

private function generateThumbnail($file)
{
    // Your existing thumbnail logic
}
```

**Test it:**
```bash
# Check if GD installed
php -r "echo extension_loaded('gd') ? 'YES' : 'NO';"

# If NO, test submission upload - should work without error
```

---

## Fix #3: Prize Pool Auto-Calculate (1 hour)

**Option A: Using Observer (Recommended)**

```php
// FILE: app/Models/Observers/CompetitionPrizeObserver.php
// ACTION: Create new file

<?php

namespace App\Models\Observers;

use App\Models\CompetitionPrize;

class CompetitionPrizeObserver
{
    public function created(CompetitionPrize $prize): void
    {
        $this->updateTotal($prize);
    }

    public function updated(CompetitionPrize $prize): void
    {
        $this->updateTotal($prize);
    }

    public function deleted(CompetitionPrize $prize): void
    {
        $this->updateTotal($prize);
    }

    private function updateTotal(CompetitionPrize $prize): void
    {
        $total = $prize->competition->prizes()->sum('cash_amount');
        $prize->competition->update(['total_prize_pool' => $total]);
    }
}
```

```php
// FILE: app/Providers/AppServiceProvider.php
// LOCATION: In boot() method

use App\Models\CompetitionPrize;
use App\Models\Observers\CompetitionPrizeObserver;

public function boot(): void
{
    CompetitionPrize::observe(CompetitionPrizeObserver::class);
}
```

**Test it:**
```bash
php artisan tinker
>>> $comp = Competition::first();
>>> $comp->prizes()->create(['cash_amount' => 1000, 'rank' => 1]);
>>> $comp->refresh()->total_prize_pool  # Should show 1000
>>> $comp->prizes()->create(['cash_amount' => 500, 'rank' => 2]);
>>> $comp->refresh()->total_prize_pool  # Should show 1500
```

---

**Option B: Quick Method (if no observer):**

```php
// FILE: app/Models/Competition.php
// LOCATION: Add this method

public function recalculatePrizePool(): void
{
    $total = $this->prizes()->sum('cash_amount');
    $this->update(['total_prize_pool' => $total]);
}
```

Then in your controller after creating prize:
```php
$competition->recalculatePrizePool();
```

---

## Fix #4: Admin Routes Verification (1 hour)

Run this bash script to verify all routes:

```bash
#!/bin/bash

# Get your admin token (replace with real token)
TOKEN="your_admin_token_here"
API="http://127.0.0.1:8000/api/v1/admin"

echo "Testing Admin Competition Routes..."

# Test 1: Get all competitions
echo "✓ GET /competitions"
curl -s -X GET "$API/competitions" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.data | length'

# Test 2: Create competition
echo "✓ POST /competitions"
curl -s -X POST "$API/competitions" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test 2026",
    "slug": "test-2026-'$(date +%s)'",
    "description": "Test competition",
    "submission_deadline": "2026-03-01",
    "status": "draft"
  }' | jq '.data.id'

# Test 3: Get one competition (replace 1 with real ID)
echo "✓ GET /competitions/1"
curl -s -X GET "$API/competitions/1" \
  -H "Authorization: Bearer $TOKEN" | jq '.data.title'

# Test 4: Calculate winners
echo "✓ POST /competitions/1/calculate-winners"
curl -s -X POST "$API/competitions/1/calculate-winners" \
  -H "Authorization: Bearer $TOKEN" | jq '.message'

# Test 5: Get winners list
echo "✓ GET /competitions/1/winners"
curl -s -X GET "$API/competitions/1/winners" \
  -H "Authorization: Bearer $TOKEN" | jq '.data | length'
```

**If any route fails (not 200/201):**

Add missing route to `routes/api.php`:

```php
// Add to admin section
Route::get('/competitions/{id}/votes/suspicious', [AdminCompetitionApiController::class, 'suspiciousVotes']);
Route::post('/competitions/{competition}/submissions/{submission}/approve', [AdminCompetitionApiController::class, 'approveSubmission']);
```

---

## Fix #5: Dashboard Count Sync (1 hour)

```php
// FILE: app/Http/Controllers/Api/AdminCompetitionApiController.php
// LOCATION: In index() method, replace current logic

public function index(Request $request)
{
    $query = Competition::query();
    
    // Build query based on filters
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    if ($request->filled('featured')) {
        $query->where('is_featured', (bool)$request->featured);
    }
    
    // Get accurate counts BEFORE pagination
    $baseQuery = clone $query;
    $stats = [
        'total' => $baseQuery->count(),
        'draft' => $baseQuery->where('status', 'draft')->count(),
        'active' => $baseQuery->where('status', 'active')->count(),
        'completed' => $baseQuery->where('status', 'completed')->count(),
        'archived' => $baseQuery->where('status', 'archived')->count(),
    ];
    
    // Now get paginated results
    $competitions = $query
        ->latest('created_at')
        ->paginate($request->get('per_page', 15));
    
    return response()->json([
        'status' => 'success',
        'stats' => $stats,
        'data' => $competitions,
        'pagination' => [
            'total' => $competitions->total(),
            'current_page' => $competitions->currentPage(),
            'per_page' => $competitions->perPage(),
        ]
    ]);
}
```

**Test it:**
```bash
php artisan tinker
>>> Competition::count()  # Note this number

# Then make API call:
curl http://127.0.0.1:8000/api/v1/admin/competitions \
  -H "Authorization: Bearer TOKEN"

# Verify stats.total matches Competition::count()
```

---

## 🔍 VERIFICATION CHECKLIST

After applying each fix, verify:

- [ ] **Fix #1:** `php artisan tinker` → `CompetitionScore::with('judge')->first()` works
- [ ] **Fix #2:** Test file upload → no error even without GD
- [ ] **Fix #3:** Add a prize → `Competition::total_prize_pool` updates automatically
- [ ] **Fix #4:** Run bash script → all routes return 200/201
- [ ] **Fix #5:** Compare `/admin/competitions` stats with `Competition::count()`

---

## 📋 IMPLEMENTATION ORDER

**Priority sequence (complete in this order):**

1. **Fix #1** (30 min) ← START HERE - Quick win
2. **Fix #2** (1.5 hours) ← High impact
3. **Fix #3** (1 hour) ← Essential for admin
4. **Fix #4** (1 hour) ← Verify completeness
5. **Fix #5** (1 hour) ← Trust/UX issue

**Total Time:** ~5 hours  
**Estimated Completion:** Today + testing tomorrow

---

## ⚠️ CRITICAL BEFORE YOU START

1. **Backup database:**
   ```bash
   mysqldump -u root photographar > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Create feature branch:**
   ```bash
   git checkout -b fix/competitions-p0-blocker
   ```

3. **Run tests before/after:**
   ```bash
   php artisan test --filter=Competition
   ```

4. **Log your changes:**
   ```bash
   git add .
   git commit -m "fix: Apply P0 competition fixes (#1-5)"
   ```

---

## 🆘 TROUBLESHOOTING

**Issue:** Relationships not loading  
**Fix:** Clear config cache `php artisan config:clear` and restart tinker

**Issue:** Prize total not updating  
**Fix:** Check observer is registered in AppServiceProvider boot()

**Issue:** Admin routes return 401  
**Fix:** Ensure Bearer token is valid and user has admin role

**Issue:** Image processing still fails  
**Fix:** Check GD status with `php -m | grep -i gd`

---

**Status:** Ready for implementation  
**Next Step:** Start with Fix #1 (30 min task)
