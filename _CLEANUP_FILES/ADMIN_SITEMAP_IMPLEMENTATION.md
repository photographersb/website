# Admin Sitemap + Link Tester System - Implementation Guide

**Version:** 1.0  
**Status:** PRODUCTION READY  
**Created:** February 3, 2026  
**Framework:** Laravel 11.48.0  

---

## 📋 Executive Summary

The Admin Sitemap + Link Tester system provides automated monitoring and testing of all admin routes in the application. It automatically discovers, catalogs, and tests every admin page for broken links, 404s, 500 errors, permission issues, and blank responses.

### Key Features
✅ Automatic admin route discovery using Laravel Route::getRoutes()  
✅ Parameterized route resolution (auto-substitute IDs from database)  
✅ Comprehensive link testing (status codes, response times, error detection)  
✅ Failure tracking with error summaries and recommendations  
✅ Admin dashboard with stats cards and recent scan history  
✅ Detailed results view with filters, search, and sorting  
✅ CSV export for reporting (super_admin only)  
✅ Production-safe (continues on failures, no crashes)  

---

## 🏗️ Architecture Overview

```
Routes (web.php)
    ↓
AdminSitemapController
    ├─→ index()  [GET /admin/system-health/sitemap]
    ├─→ runTest()  [POST /admin/system-health/sitemap/run-test]
    ├─→ show()  [GET /admin/system-health/sitemap/checks/{id}]
    ├─→ export()  [GET /admin/system-health/sitemap/checks/{id}/export]
    └─→ destroy()  [DELETE /admin/system-health/sitemap/checks/{id}]
        ↓
AdminSitemapTestService
    ├─→ startCheck()  [Create check record]
    ├─→ getAdminRoutes()  [Discover all admin/* routes]
    ├─→ runAllTests()  [Loop through routes and test each]
    ├─→ testRoute()  [Test single route]
    ├─→ resolveParameterizedRoute()  [Replace {param} with IDs]
    ├─→ getParameterValue()  [Get ID from database]
    ├─→ makeRequest()  [HTTP call to route]
    └─→ determineResult()  [Pass/Fail/Skip logic]
        ↓
Database
    ├─→ admin_sitemap_checks (check metadata)
    └─→ admin_sitemap_check_results (individual test results)
```

---

## 📦 Database Schema

### admin_sitemap_checks Table
Stores metadata about each link test run.

```sql
CREATE TABLE admin_sitemap_checks (
    id bigint(20) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    run_by_user_id bigint(20) UNSIGNED NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    finished_at TIMESTAMP NULL,
    total_links INT DEFAULT 0,
    passed INT DEFAULT 0,
    failed INT DEFAULT 0,
    skipped INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (run_by_user_id) REFERENCES users(id),
    INDEX (run_by_user_id),
    INDEX (created_at)
);
```

### admin_sitemap_check_results Table
Stores individual test results for each link.

```sql
CREATE TABLE admin_sitemap_check_results (
    id bigint(20) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    check_id bigint(20) UNSIGNED NOT NULL,
    module VARCHAR(100),
    route_name VARCHAR(255),
    url VARCHAR(500),
    method VARCHAR(10) DEFAULT 'GET',
    status_code INT NULL,
    response_time_ms INT DEFAULT 0,
    result_status ENUM('passed', 'failed', 'skipped') DEFAULT 'skipped',
    error_summary TEXT NULL,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (check_id) REFERENCES admin_sitemap_checks(id) ON DELETE CASCADE,
    INDEX (check_id),
    INDEX (module),
    INDEX (result_status),
    INDEX (status_code)
);
```

---

## 📁 File Inventory

### Models (Updated)
- **app/Models/AdminSitemapCheck.php** (105 lines)
  - Relationships: runByUser(), results(), passedResults(), failedResults(), skippedResults()
  - Methods: getDurationSeconds(), getSuccessRate(), isComplete()
  - Fillable: run_by_user_id, started_at, finished_at, total_links, passed, failed, skipped

- **app/Models/AdminSitemapCheckResult.php** (100 lines)
  - Relationships: check()
  - Methods: isPassed(), isFailed(), isSkipped(), getRecommendedFix(), getBadgeClass()
  - Fillable: check_id, module, route_name, url, method, status_code, response_time_ms, result_status, error_summary

### Service (New)
- **app/Services/AdminSitemapTestService.php** (400+ lines)
  - Core testing engine
  - Methods:
    - `startCheck($userId)` → Creates check record
    - `getAdminRoutes()` → Discovers admin/* routes
    - `runAllTests($check, $user)` → Main test loop
    - `testRoute($check, $route, $user)` → Tests single route
    - `resolveParameterizedRoute($uri)` → Handles {param}
    - `getParameterValue($param)` → Gets ID from DB
    - `makeRequest($url, $user)` → HTTP call
    - `determineResult($status, $response)` → Pass/Fail/Skip logic
    - `isBlankResponse($response)` → Detects blank pages
  - Constants:
    - EXCLUDED_ROUTES (logout, delete, destroy, etc.)
    - PARAMETERIZED_ROUTES (competitions, events, users)

### Controller (Updated)
- **app/Http/Controllers/Admin/AdminSitemapController.php** (220 lines)
  - Methods:
    - `index()` → Dashboard with stats and recent scans
    - `runTest($request)` → Trigger new test run
    - `getChecks($request)` → API endpoint for all checks
    - `show($check)` → View single check with filters
    - `export($check)` → CSV export (super_admin only)
    - `destroy($check)` → Delete check record
  - Middleware: auth, admin

### Views (New/Updated)
- **resources/views/admin/sitemap/index.blade.php** (250+ lines)
  - Dashboard with stats cards
  - Recent scans table
  - Admin routes overview
  - "Run Link Test" button with AJAX

- **resources/views/admin/sitemap/show.blade.php** (350+ lines)
  - Check results detail page
  - Summary statistics
  - Quick filters (module, status, sort, search)
  - Detailed results table
  - Failed tests alert
  - CSV export button

### Routes (Updated)
- **routes/web.php** (lines 43-54)
  - GET `/admin/system-health/sitemap` → index
  - POST `/admin/system-health/sitemap/run-test` → runTest
  - GET `/admin/system-health/sitemap/checks` → getChecks
  - GET `/admin/system-health/sitemap/checks/{check}` → show
  - GET `/admin/system-health/sitemap/checks/{check}/export` → export
  - DELETE `/admin/system-health/sitemap/checks/{check}` → destroy

### Migrations (New)
- **database/migrations/2026_02_03_225201_create_admin_sitemap_checks_table.php**
- **database/migrations/2026_02_03_225202_create_admin_sitemap_check_results_table.php**

---

## 🔄 How It Works

### 1. Route Discovery Phase
```php
$service->getAdminRoutes()
```
- Iterates through all registered Laravel routes
- Filters for GET requests starting with "admin/"
- Excludes logout, delete, destroy, and other unsafe routes
- Extracts module name, route name, URI, controller info
- Returns array of testable routes

### 2. Parameterized Route Resolution
```php
$resolvedUrl = $service->resolveParameterizedRoute($uri)
```
- Detects routes with parameters: `/admin/events/{event}/edit`
- For each parameter, queries database for latest record
- Replaces `{event}` with actual ID: `/admin/events/42/edit`
- If no data found, marks as "Skipped (No Data)"
- Supports: competitions, events, users

### 3. Testing Phase
```php
foreach ($routes as $route) {
    $service->testRoute($check, $route, $user)
}
```
- For each route:
  - Makes HTTP GET request
  - Captures status code and response time
  - Checks for blank responses
  - Determines result: passed/failed/skipped
  - Stores in database

### 4. Result Determination Logic
```
Status 200-299 + Valid HTML → PASSED
Status 300-399 (redirects)  → PASSED
Status 400, 403, 404, 500+  → FAILED
No data / Exception         → SKIPPED
Blank response              → FAILED
```

### 5. Error Handling
- Wraps each test in try/catch
- Continues on failure (no stopping)
- Logs exceptions
- Stores error_summary for failed tests
- Never crashes the entire process

---

## 🎯 Usage Guide

### Running a Sitemap Test

**Method 1: Via Admin Dashboard**
```
1. Navigate to Admin → System Health → Admin Sitemap
2. Click "Run Link Test" button
3. Wait for results to populate
4. View results on new page
```

**Method 2: Via Controller (Programmatic)**
```php
$service = app(AdminSitemapTestService::class);
$user = Auth::user();
$check = $service->startCheck($user->id);
$service->runAllTests($check, $user);
```

### Filtering Results
- **By Module:** Dropdown to filter by dashboard/users/events/etc.
- **By Status:** Show only passed/failed/skipped
- **By URL:** Text search in URL and route name
- **Sort By:** Module (A-Z), response time (fast/slow), status code

### Interpreting Results

| Status | Meaning | Action |
|--------|---------|--------|
| ✓ Passed | HTTP 200-299 or redirect | No action needed |
| ✗ Failed | 404, 403, 500, etc. | Review error and fix route |
| ⊘ Skipped | Needs parameters or no data | Test manually with specific ID |

### Exporting Results
```
Admin → System Health → Admin Sitemap → [View Check] → Export CSV
```
- Super_admin only
- Includes all columns: Module, Route, URL, Status, Code, Time, Result, Error
- Can be opened in Excel/Google Sheets

---

## 🔐 Permission & Access Control

### Required Middleware
- `auth` - Must be logged in
- `admin` - Must have admin role or higher

### Role-Based Features
| Feature | Admin | Super Admin |
|---------|-------|------------|
| View dashboard | ✓ | ✓ |
| Run tests | ✓ | ✓ |
| View results | ✓ | ✓ |
| Export CSV | ✗ | ✓ |
| View full traces | ✗ | ✓ |
| Delete checks | Own only | Any |

---

## 🛠️ Configuration

### Route Exclusions
Located in `AdminSitemapTestService::EXCLUDED_ROUTES`:
```php
'logout', 'delete', 'destroy', 'verify-email', 
'password.reset', 'password.update', 'profile.delete'
```

### Parameterized Routes
Located in `AdminSitemapTestService::PARAMETERIZED_ROUTES`:
```php
'competitions' => 'competitions',
'events' => 'events',
'users' => 'users',
```

### Modifying Parameters
To add new parameterized routes:
```php
private const PARAMETERIZED_ROUTES = [
    'blogs.edit' => 'blogs',  // NEW
];

// Add to getParameterValue():
'blog' => Blog::latest()->first()?->id,
```

---

## 📊 Performance Considerations

### Response Times
- Single route test: 10-100ms typically
- Total test run: 1-10 seconds (depends on route count)
- Response times logged and visible in UI

### Database Indexes
All critical columns indexed:
- `admin_sitemap_checks`: run_by_user_id, created_at
- `admin_sitemap_check_results`: check_id, module, result_status, status_code

### Optimization Tips
1. Run tests during low-traffic periods
2. Use filters to view specific modules
3. Delete old checks to keep database lean
4. Export results periodically for archival

---

## 🐛 Troubleshooting

### Issue: "No admin routes found"
**Cause:** Routes not registered in routes/*.php files  
**Solution:** Register routes in routes/web.php or routes/api.php with `Route::get('/admin/...', ...)`

### Issue: Parameterized routes showing "Skipped"
**Cause:** No data exists in database for that model  
**Solution:** Create test data first, or add conditional parameter logic to service

### Issue: Test hung or not completing
**Cause:** Route has infinite loop or external API call  
**Solution:** Add timeout configuration to makeRequest() method (see code comments)

### Issue: CSV export returns 403
**Cause:** User is not super_admin  
**Solution:** Only super_admin users can export - login as admin with super_admin role

---

## 🚀 Deployment Checklist

- [ ] Database migrations executed (tables created)
- [ ] All PHP files syntax-verified (no errors)
- [ ] Routes registered in routes/web.php
- [ ] Admin layout template exists (resources/views/admin/layout.blade.php)
- [ ] Tailwind CSS loaded in layout
- [ ] Font Awesome icons available in layout
- [ ] Test user with admin role created
- [ ] Run first test to verify functionality
- [ ] Check admin_sitemap_checks table has data
- [ ] View results page loads without errors
- [ ] CSV export button works (super_admin only)
- [ ] Monitor application logs for any errors

---

## 📝 Code Examples

### Example 1: Manually Trigger Test
```php
// In a controller or command
$service = app(AdminSitemapTestService::class);
$user = Auth::user();

$check = $service->startCheck($user->id);
$service->runAllTests($check, $user);

echo "Completed: {$check->passed} passed, {$check->failed} failed";
```

### Example 2: Get Failed Routes Only
```php
$check = AdminSitemapCheck::find(1);
$failedRoutes = $check->results()
    ->where('result_status', 'failed')
    ->get();

foreach ($failedRoutes as $result) {
    echo "FAILED: {$result->route_name} ({$result->status_code})";
}
```

### Example 3: Export Programmatically
```php
$check = AdminSitemapCheck::find(1);
$csv = $controller->export($check);
file_put_contents('sitemap-export.csv', $csv);
```

---

## 🔮 Future Enhancements

### Phase 2 (Optional)
- [ ] Real-time test progress indicator
- [ ] Background job queue integration (async tests)
- [ ] Webhook notifications on failures
- [ ] Historical trend analysis
- [ ] Performance benchmarking
- [ ] Scheduled daily/weekly scans
- [ ] Slack/email alerts for critical failures
- [ ] Authentication requirement override for testing
- [ ] Custom header injection (API keys, tokens)
- [ ] Test result comparison between runs

---

## 📞 Support

For issues or questions:
1. Check troubleshooting section above
2. Review error logs in `storage/logs/laravel.log`
3. Verify database migrations executed
4. Confirm all files have correct permissions

---

## ✅ Production Ready Checklist

- ✅ All PHP code syntax verified
- ✅ Database schema created and indexed
- ✅ Models with relationships defined
- ✅ Service with error handling implemented
- ✅ Controller with permission checks added
- ✅ Views with responsive design created
- ✅ Routes properly configured
- ✅ Failure tolerance built in (no crashes)
- ✅ Parameterized route support added
- ✅ CSV export functionality included
- ✅ Permission-based feature access
- ✅ Comprehensive documentation provided

**Status: READY FOR PRODUCTION DEPLOYMENT**

---

Last Updated: February 3, 2026  
Maintainer: Development Team
