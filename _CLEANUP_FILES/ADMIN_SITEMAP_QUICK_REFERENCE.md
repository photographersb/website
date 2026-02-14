# Admin Sitemap + Link Tester - Quick Reference Guide

**Last Updated:** February 3, 2026  
**Status:** PRODUCTION READY

---

## 🚀 Quick Start

### Access the Dashboard
```
URL: http://localhost/admin/system-health/sitemap
Requires: Logged in as admin/super_admin
```

### Run Your First Test
1. Click **"Run Link Test"** button (top right)
2. Wait 5-15 seconds for test to complete
3. Results page loads automatically
4. Review stats and failed routes

---

## 📍 File Locations

| Purpose | File Path |
|---------|-----------|
| **Service** | `app/Services/AdminSitemapTestService.php` |
| **Controller** | `app/Http/Controllers/Admin/AdminSitemapController.php` |
| **Models** | `app/Models/AdminSitemap{Check,CheckResult}.php` |
| **Views** | `resources/views/admin/sitemap/{index,show}.blade.php` |
| **Routes** | `routes/web.php` (lines 43-54) |
| **Migrations** | `database/migrations/2026_02_03_225*.php` |

---

## 📊 Database Tables

| Table | Purpose | Key Columns |
|-------|---------|------------|
| `admin_sitemap_checks` | Test run metadata | id, run_by_user_id, total_links, passed, failed, skipped, created_at |
| `admin_sitemap_check_results` | Individual test results | check_id, route_name, url, status_code, response_time_ms, result_status, error_summary |

---

## 🔗 API Routes

### Dashboard & Management
```
GET  /admin/system-health/sitemap               → Show dashboard
POST /admin/system-health/sitemap/run-test      → Start new test (AJAX)
GET  /admin/system-health/sitemap/checks        → Get all checks (JSON)
GET  /admin/system-health/sitemap/checks/{id}   → View check details
GET  /admin/system-health/sitemap/checks/{id}/export → CSV export (super_admin only)
DELETE /admin/system-health/sitemap/checks/{id} → Delete check
```

---

## 🎯 Key Methods

### AdminSitemapTestService
```php
// Start a new check
$check = $service->startCheck($user->id);

// Get all admin routes
$routes = $service->getAdminRoutes();

// Run all tests
$service->runAllTests($check, $user);

// Test single route
$service->testRoute($check, $route, $user);

// Resolve parameters
$url = $service->resolveParameterizedRoute('/admin/events/{event}/edit');
```

### Models
```php
// Check relationships
$check->runByUser;     // User who ran the check
$check->results;       // All test results
$check->passed;        // Passed count
$check->failed;        // Failed count
$check->skipped;       // Skipped count

// Check methods
$check->getDurationSeconds();   // How long test took
$check->getSuccessRate();       // Success percentage
$check->isComplete();           // Test finished?

// Result methods
$result->isPassed();   // bool
$result->isFailed();   // bool
$result->isSkipped();  // bool
$result->getRecommendedFix();  // Fix suggestion
$result->getBadgeClass();      // CSS class for badge color
```

---

## 🔍 Common Queries

### Get Latest Check
```php
$check = AdminSitemapCheck::latest()->first();
echo $check->passed . ' passed, ' . $check->failed . ' failed';
```

### Get Failed Routes Only
```php
$failed = AdminSitemapCheck::find(1)
    ->results()
    ->where('result_status', 'failed')
    ->get();

foreach ($failed as $route) {
    echo "{$route->route_name}: {$route->error_summary}\n";
}
```

### Get Slowest Routes
```php
$slowest = AdminSitemapCheckResult::where('check_id', 1)
    ->orderBy('response_time_ms', 'desc')
    ->limit(10)
    ->get();
```

### Get Routes by Module
```php
$results = AdminSitemapCheckResult::where('check_id', 1)
    ->where('module', 'events')
    ->get();
```

---

## 🛠️ Customization

### Add New Parameterized Route Type
File: `app/Services/AdminSitemapTestService.php`

```php
// In PARAMETERIZED_ROUTES constant
private const PARAMETERIZED_ROUTES = [
    'blogs.edit' => 'blogs',  // ADD THIS
];

// In getParameterValue() method
'blog' => Blog::latest()->first()?->id,  // ADD THIS
```

### Exclude Additional Routes
File: `app/Services/AdminSitemapTestService.php`

```php
private const EXCLUDED_ROUTES = [
    'logout',
    'delete',
    'my-new-route',  // ADD THIS
];
```

### Change Route Prefix
File: `routes/web.php`

```php
// From:
Route::prefix('admin')->middleware('auth')->group(function () {

// To:
Route::prefix('dashboard')->middleware('auth')->group(function () {
```

---

## 🐛 Troubleshooting

### Problem: "No admin routes found"
**Solution:** Verify routes exist with prefix 'admin/'
```bash
php artisan route:list | grep admin
```

### Problem: Parameterized routes showing as Skipped
**Solution:** Create test data first
```bash
php artisan tinker
>>> Competition::factory()->create();
```

### Problem: Test takes too long (>30s)
**Solution:** Check for routes with external API calls or timeouts
- Review storage/logs/laravel.log
- Check makeRequest() implementation
- Consider adding timeout handling

### Problem: CSV export shows 403 Forbidden
**Solution:** Only super_admin can export
- Verify user has role='super_admin'
- Check middleware in AdminSitemapController

### Problem: Results not saving to database
**Solution:** Verify foreign keys
```sql
SELECT * FROM admin_sitemap_checks;
SELECT * FROM admin_sitemap_check_results;
```

---

## 📈 Performance Tips

1. **Run during low traffic:** Tests make many HTTP requests
2. **Check database indexes:** Ensure admin_sitemap_* tables are indexed
3. **Archive old tests:** Delete checks > 30 days old:
   ```php
   AdminSitemapCheck::where('created_at', '<', now()->subDays(30))->delete();
   ```
4. **Use filters:** Don't load all 1000 results at once, use module/status filters

---

## 🔐 Security Notes

- ✅ Only auth users can access
- ✅ Only admin role or higher
- ✅ Super admin only for CSV export
- ✅ User can only delete own checks (or super_admin any)
- ⚠️ Tests make HTTP requests to admin routes - ensure routes are safe
- ⚠️ Service doesn't authenticate as any user - uses system access

---

## 📝 Result Status Meanings

| Status | Meaning | Count |
|--------|---------|-------|
| **Passed** | HTTP 200-299 OR valid redirect | Success |
| **Failed** | 404, 403, 500, or other error | Error |
| **Skipped** | Route needs params but no data, or exception | Not Tested |

---

## 📊 Dashboard Interpretation

### Success Rate < 80%
- 🔴 Investigate failed routes
- Fix 404s and 500s
- Rerun test

### Many Skipped Routes
- 🟡 Create test data for parameterized routes
- Or test manually with specific IDs

### Response Times > 500ms
- 🟡 Investigate slow routes
- Check database queries
- Optimize controller logic

---

## 🚨 Alert Levels

| Metric | Alert Level |
|--------|------------|
| Failed routes: 0 | ✅ Great |
| Failed routes: 1-3 | 🟡 Review |
| Failed routes: >5 | 🔴 Critical |
| Success rate: >90% | ✅ Excellent |
| Success rate: 70-90% | 🟡 Good |
| Success rate: <70% | 🔴 Needs Work |

---

## 🔧 Manual Test Commands

### Via Tinker
```bash
php artisan tinker
>>> $service = app(\App\Services\AdminSitemapTestService::class);
>>> $user = \App\Models\User::first();
>>> $check = $service->startCheck($user->id);
>>> $service->runAllTests($check, $user);
>>> $check->refresh();
>>> echo "{$check->passed} passed, {$check->failed} failed";
```

### Via Route Test
```bash
# Access dashboard
curl http://localhost/admin/system-health/sitemap

# Trigger test
curl -X POST http://localhost/admin/system-health/sitemap/run-test \
  -H "X-CSRF-TOKEN: $(csrf_token)" \
  -H "Authorization: Bearer $(auth_token)"
```

---

## 📋 Maintenance Schedule

| Task | Frequency | Command |
|------|-----------|---------|
| Run sitemap test | Weekly | Click "Run Link Test" |
| Review failed routes | After each test | Check results page |
| Archive old tests | Monthly | `AdminSitemapCheck::where('created_at', '<', now()->subDays(30))->delete();` |
| Backup database | Daily | Standard backup routine |
| Review logs | Weekly | `tail -f storage/logs/laravel.log` |

---

## 🎓 Learning Resources

- **Implementation Guide:** `ADMIN_SITEMAP_IMPLEMENTATION.md`
- **Verification Checklist:** `ADMIN_SITEMAP_VERIFICATION_CHECKLIST.md`
- **Laravel Routes:** `https://laravel.com/docs/routing`
- **Laravel Testing:** `https://laravel.com/docs/testing`

---

## 🆘 Support & Questions

**Where to look first:**
1. Check this guide (you're reading it!)
2. Review ADMIN_SITEMAP_IMPLEMENTATION.md
3. Check storage/logs/laravel.log for errors
4. Verify database tables exist

**If issue persists:**
1. Verify all files present and syntax correct
2. Ensure database migrations ran
3. Check user has admin role
4. Review recent code changes

---

## ✅ Production Checklist Before Deploying

- [ ] Database migrations ran successfully
- [ ] All PHP files syntax verified
- [ ] Admin user created with admin/super_admin role
- [ ] First test run completed successfully
- [ ] Results visible in database and UI
- [ ] CSV export works for super_admin
- [ ] Filters and search working on results page
- [ ] No errors in storage/logs/laravel.log
- [ ] Performance acceptable (< 15s for full test)
- [ ] Permissions enforced correctly

---

**Version:** 1.0  
**Last Updated:** February 3, 2026  
**Maintainer:** Development Team
