# Admin Sitemap + Link Tester - Verification Checklist

**Date:** February 3, 2026  
**Status:** READY FOR VERIFICATION  
**Test Environment:** Localhost & Production

---

## A) DATABASE VERIFICATION

### A.1: Tables Created
- [ ] `admin_sitemap_checks` table exists
  - Verify: `SELECT * FROM admin_sitemap_checks LIMIT 1;`
  - Expected fields: id, run_by_user_id, started_at, finished_at, total_links, passed, failed, skipped
  
- [ ] `admin_sitemap_check_results` table exists
  - Verify: `SELECT * FROM admin_sitemap_check_results LIMIT 1;`
  - Expected fields: id, check_id, module, route_name, url, method, status_code, response_time_ms, result_status, error_summary

### A.2: Indexes
- [ ] Indexes created on admin_sitemap_checks
  - Verify: `SHOW INDEX FROM admin_sitemap_checks;`
  - Expected: run_by_user_id, created_at
  
- [ ] Indexes created on admin_sitemap_check_results
  - Verify: `SHOW INDEX FROM admin_sitemap_check_results;`
  - Expected: check_id, module, result_status, status_code

### A.3: Foreign Keys
- [ ] Foreign key admin_sitemap_checks.run_by_user_id → users.id
  - Verify: `SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS WHERE TABLE_NAME='admin_sitemap_checks';`

- [ ] Foreign key admin_sitemap_check_results.check_id → admin_sitemap_checks.id with CASCADE DELETE
  - Verify: `SELECT * FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS WHERE TABLE_NAME='admin_sitemap_check_results';`

---

## B) FILE STRUCTURE VERIFICATION

### B.1: Models
- [ ] `app/Models/AdminSitemapCheck.php` exists and is valid PHP
  - Verify: `php -l app/Models/AdminSitemapCheck.php`
  - Expected: "No syntax errors detected"
  
- [ ] `app/Models/AdminSitemapCheckResult.php` exists and is valid PHP
  - Verify: `php -l app/Models/AdminSitemapCheckResult.php`
  - Expected: "No syntax errors detected"

### B.2: Service
- [ ] `app/Services/AdminSitemapTestService.php` exists and is valid PHP
  - Verify: `php -l app/Services/AdminSitemapTestService.php`
  - Expected: "No syntax errors detected"

### B.3: Controller
- [ ] `app/Http/Controllers/Admin/AdminSitemapController.php` exists and is valid PHP
  - Verify: `php -l app/Http/Controllers/Admin/AdminSitemapController.php`
  - Expected: "No syntax errors detected"

### B.4: Views
- [ ] `resources/views/admin/sitemap/index.blade.php` exists
  - Check: File present and contains "Run Link Test" button
  
- [ ] `resources/views/admin/sitemap/show.blade.php` exists
  - Check: File present and contains results table

### B.5: Routes
- [ ] Routes registered in `routes/web.php`
  - Verify: `grep -n "admin.sitemap" routes/web.php`
  - Expected routes:
    - GET /admin/system-health/sitemap (name: admin.sitemap.index)
    - POST /admin/system-health/sitemap/run-test (name: admin.sitemap.run-test)
    - GET /admin/system-health/sitemap/checks (name: admin.sitemap.checks)
    - GET /admin/system-health/sitemap/checks/{check} (name: admin.sitemap.show)
    - GET /admin/system-health/sitemap/checks/{check}/export (name: admin.sitemap.export)
    - DELETE /admin/system-health/sitemap/checks/{check} (name: admin.sitemap.destroy)

---

## C) LARAVEL BOOTSTRAP VERIFICATION

### C.1: ServiceProvider Registration (If needed)
- [ ] Service registered in app/Providers/AppServiceProvider.php (if using service binding)
  - Check: `$this->app->singleton(AdminSitemapTestService::class, ...);`

### C.2: Model Relationships
- [ ] AdminSitemapCheck model relationships work
  ```php
  $check = AdminSitemapCheck::first();
  assert($check->runByUser instanceof User);
  assert($check->results instanceof Collection);
  ```

- [ ] AdminSitemapCheckResult model relationships work
  ```php
  $result = AdminSitemapCheckResult::first();
  assert($result->check instanceof AdminSitemapCheck);
  ```

---

## D) ROUTE DISCOVERY VERIFICATION

### D.1: Route Discovery Works
- [ ] Admin routes properly discovered
  ```php
  $service = app(AdminSitemapTestService::class);
  $routes = $service->getAdminRoutes();
  assert(count($routes) > 0, 'No admin routes found');
  ```

- [ ] Routes contain expected fields
  ```php
  foreach ($routes as $route) {
      assert(isset($route['module']), 'Missing module');
      assert(isset($route['route_name']), 'Missing route_name');
      assert(isset($route['uri']), 'Missing uri');
  }
  ```

### D.2: Excluded Routes Properly Filtered
- [ ] Logout routes excluded
  - Verify: `grep -c 'logout' routes/*; // Should return 0 in results`
  
- [ ] Delete/Destroy routes excluded
  - Verify: No routes with DELETE method in results

### D.3: Route Count Reasonable
- [ ] Expected: 15-50 admin routes in typical Laravel app
  - Verify: `count($routes) >= 10`

---

## E) PARAMETERIZED ROUTE HANDLING

### E.1: Parameter Detection
- [ ] Routes with {param} are properly detected
  ```php
  $routes = $service->getAdminRoutes();
  $hasParams = collect($routes)
      ->some(fn($r) => str_contains($r['uri'], '{'));
  assert($hasParams, 'No parameterized routes found');
  ```

### E.2: Parameter Resolution
- [ ] Parameters resolved with database IDs
  ```php
  $resolved = $service->resolveParameterizedRoute('admin/competitions/{competition}/edit');
  assert(is_numeric(str_replace('admin/competitions/', '', $resolved)));
  ```

### E.3: No-Data Handling
- [ ] Routes without data marked as Skipped
  - Create fresh database with minimal data
  - Run test
  - Verify: Some results have result_status='skipped'

---

## F) LINK TESTING ENGINE

### F.1: Test Execution
- [ ] Service::runAllTests() executes without crashing
  ```php
  $user = User::first();
  $check = $service->startCheck($user->id);
  $service->runAllTests($check, $user); // Should complete
  ```

### F.2: Status Code Detection
- [ ] Routes returning 200 marked as passed
- [ ] Routes returning 404 marked as failed
- [ ] Routes returning 500 marked as failed
- [ ] Routes returning 403 marked as failed (unless intended)

### F.3: Response Time Measurement
- [ ] Response times are positive integers
  ```php
  $results = $check->results;
  foreach ($results as $result) {
      assert($result->response_time_ms >= 0);
  }
  ```

### F.4: Error Summary Generation
- [ ] Failed tests have error_summary
  ```php
  $failed = $check->results()
      ->where('result_status', 'failed')
      ->first();
  assert(!empty($failed->error_summary));
  ```

### F.5: Blank Response Detection
- [ ] Blank responses detected and marked failed
- [ ] Non-blank HTML responses marked passed

---

## G) ERROR RESILIENCE

### G.1: Exception Handling
- [ ] Service continues on individual route failures
  - Create route that throws exception
  - Run test
  - Verify: Other routes still tested, check completes

### G.2: No Crashes on 500 Errors
- [ ] Routes returning 500 don't crash test
  - Verify: result_status='failed' recorded, process continues

### G.3: Database Constraint Violations
- [ ] Tests handle foreign key constraints
- [ ] Tests handle unique constraint violations

---

## H) DATABASE RECORD STORAGE

### H.1: Check Record Created
- [ ] AdminSitemapCheck record created when test starts
  ```php
  $before = AdminSitemapCheck::count();
  $check = $service->startCheck($user->id);
  $after = AdminSitemapCheck::count();
  assert($after === $before + 1);
  ```

### H.2: Check Record Updated
- [ ] After test completes:
  ```php
  $check->refresh();
  assert($check->finished_at !== null);
  assert($check->total_links > 0);
  assert($check->passed >= 0);
  assert($check->failed >= 0);
  ```

### H.3: Result Records Created
- [ ] One result per route tested
  ```php
  $check->refresh();
  assert($check->results()->count() === $check->total_links);
  ```

### H.4: Data Integrity
- [ ] passed + failed + skipped = total_links
  ```php
  $check->refresh();
  $sum = $check->passed + $check->failed + $check->skipped;
  assert($sum === $check->total_links);
  ```

---

## I) ADMIN DASHBOARD UI

### I.1: Dashboard Loads
- [ ] Navigate to `/admin/system-health/sitemap`
- [ ] Page loads without 500 error
- [ ] Tailwind CSS styling applied

### I.2: Stats Cards Display
- [ ] "Total Routes" card shows correct number
- [ ] "Passed" card shows green color
- [ ] "Failed" card shows red color
- [ ] "Skipped" card shows yellow color
- [ ] Success rate percentage displayed

### I.3: Recent Scans Table
- [ ] Shows up to 5 recent scans
- [ ] Each row shows: Date, Run By, Routes Tested, Results
- [ ] "View Details" link works

### I.4: Admin Routes Overview
- [ ] Lists all admin routes to be tested
- [ ] Each route shows: Name, Module, URI, Controller

---

## J) RUN LINK TEST BUTTON

### J.1: Button Functionality
- [ ] "Run Link Test" button present
- [ ] Click triggers AJAX POST to /admin/system-health/sitemap/run-test
- [ ] Button shows loading state "Running..."

### J.2: Test Completion
- [ ] After test completes (5-15 seconds typically):
  - Browser redirected to check results page
  - Results display with statistics
  - Table populated with test results

### J.3: Error Handling
- [ ] If test fails, error message displayed
- [ ] User can retry
- [ ] No page refresh required

---

## K) CHECK RESULTS PAGE

### K.1: Summary Statistics
- [ ] Total Routes shown
- [ ] Passed count and percentage
- [ ] Failed count
- [ ] Skipped count
- [ ] Duration displayed (seconds)
- [ ] Success rate progress bar

### K.2: Filters Working
- [ ] Module filter dropdown works
  - Select a module, results filter
- [ ] Status filter works
  - Select "failed", only failures show
  - Select "passed", only passes show
- [ ] Sort by works
  - "Slowest First" sorts by response_time DESC
  - "Fastest First" sorts by response_time ASC

### K.3: Search Functionality
- [ ] Search by URL works
- [ ] Search by route name works
- [ ] Results update on keystroke

### K.4: Results Table
- [ ] Shows columns: Module, Route Name, URL, Status Code, Response Time, Result, Error
- [ ] Each result row has appropriate badge color
- [ ] URL clickable (opens in new tab)
- [ ] Failed rows highlighted

### K.5: Failed Tests Alert
- [ ] Shows if failures exist
- [ ] Lists up to 5 failures with details
- [ ] Shows "X more failures" if > 5

---

## L) CSV EXPORT

### L.1: Export Button Visible
- [ ] Super Admin: Export button visible
- [ ] Regular Admin: Export button hidden/disabled
- [ ] Guest: No access to page

### L.2: Export Functionality
- [ ] Click "Export CSV" downloads file
- [ ] Filename format: sitemap-check-{id}.csv
- [ ] Content type: text/csv

### L.3: CSV Content
- [ ] Header row: Module, Route Name, URL, Method, Status Code, Response Time, Result Status, Error Summary
- [ ] Data rows: One per tested route
- [ ] Proper CSV escaping (commas, quotes)
- [ ] Opens correctly in Excel/Google Sheets

---

## M) DETECTED 404 ERRORS

### M.1: Find 404 Routes
- [ ] Run test to completion
- [ ] Check results for status_code=404
- [ ] Verify result_status='failed'
- [ ] Expected: Likely 0-2 routes (app should be complete)

### M.2: Fix Broken Routes
For any 404 found:
- [ ] Route properly registered in routes/web.php or routes/api.php
- [ ] URI matches between route definition and test discovery
- [ ] Controller and method exist
- [ ] Run test again
- [ ] Verify fixed route now returns 200/pass

---

## N) PERMISSION VERIFICATION

### N.1: Admin Access
- [ ] Login as admin user
- [ ] Can access /admin/system-health/sitemap
- [ ] Can run tests
- [ ] Can view all results

### N.2: Super Admin Access
- [ ] Login as super_admin user
- [ ] Can see "Export CSV" button
- [ ] CSV export works
- [ ] Can view all traces

### N.3: Guest Access (No Auth)
- [ ] Not logged in
- [ ] Navigate to /admin/system-health/sitemap
- [ ] Redirected to login page

### N.4: Non-Admin User Access
- [ ] Login as regular (photographer/user) account
- [ ] Try to access /admin/system-health/sitemap
- [ ] Forbidden/redirected (403 or to dashboard)

---

## O) PERFORMANCE TESTING

### O.1: Single Route Test
- [ ] Test execution time: 10-100ms per route
- [ ] Total for 20 routes: 200-2000ms

### O.2: Full Sitemap Test
- [ ] Complete run time: 2-15 seconds (varies by route count)
- [ ] No memory overflow
- [ ] No timeout issues

### O.3: Database Performance
- [ ] Creating check record: <50ms
- [ ] Inserting 50 result records: <500ms total
- [ ] Querying results for display: <100ms

### O.4: UI Performance
- [ ] Dashboard loads: <500ms
- [ ] Results page loads: <500ms
- [ ] Filter/search updates: <200ms

---

## P) EDGE CASES

### P.1: No Routes Scenario
- [ ] If somehow app has 0 admin routes:
  - Check created with total_links=0
  - Results page shows "No results"
  - No errors thrown

### P.2: All Routes Skip
- [ ] If all routes require params and no data:
  - All results: result_status='skipped'
  - Check: skipped equals total_links
  - No failures recorded

### P.3: All Routes Fail
- [ ] Simulate by breaking all admin routes (test only):
  - Check completes
  - All results: result_status='failed'
  - Dashboard shows 0% success rate

### P.4: Large Result Set
- [ ] With 100+ routes tested:
  - Pagination works (25 per page)
  - Filters still responsive
  - Export completes without timeout

---

## Q) LOGGING & MONITORING

### Q.1: Logs Created
- [ ] Check `storage/logs/laravel.log`
- [ ] No errors when running tests
- [ ] Expected: "AdminSitemapTestService completed X routes"

### Q.2: Errors Captured
- [ ] Deliberately throw exception in one route
- [ ] Run test
- [ ] Error logged
- [ ] Test continues
- [ ] Check storage/logs/laravel.log for error entry

---

## R) DEPLOYMENT SCENARIOS

### R.1: Localhost Testing
- [ ] All tests pass on localhost
- [ ] At least 1 test run completed
- [ ] Results visible in database
- [ ] Dashboard displays stats

### R.2: Production Simulation
- [ ] Migrate to production environment
- [ ] Create admin user if needed
- [ ] Run one sitemap test
- [ ] Verify all results recorded
- [ ] Check performance acceptable

### R.3: Database Backup/Restore
- [ ] Backup database before running test
- [ ] Run test
- [ ] Restore backup
- [ ] Verify table structure intact
- [ ] Run test again

---

## S) REGRESSION TESTING

### S.1: No Interference with Other Features
- [ ] After running sitemap tests:
  - Other admin routes still work
  - User authentication still works
  - Database integrity maintained
  - Application logs clean

### S.2: Multiple Consecutive Runs
- [ ] Run 3 tests in a row
- [ ] Each creates new check record
- [ ] All complete successfully
- [ ] Database queries efficient

---

## T) SIGN-OFF

### Final Verification Checklist
- [ ] All PHP syntax verified (A✓, B✓)
- [ ] Database schema correct (A✓)
- [ ] Models working (C✓)
- [ ] Route discovery working (D✓, E✓)
- [ ] Testing engine functional (F✓, G✓)
- [ ] Data stored correctly (H✓)
- [ ] UI loads and functions (I✓, J✓, K✓)
- [ ] Export works (L✓)
- [ ] Permissions enforced (N✓)
- [ ] Performance acceptable (O✓)
- [ ] Edge cases handled (P✓)
- [ ] Production ready (R✓)

### Approval
- [ ] **Developer:** _________________ Date: _______
- [ ] **QA Lead:** _________________ Date: _______
- [ ] **Project Manager:** _________________ Date: _______

### Deployment Authority
- [ ] Approved for production deployment
- [ ] Documentation complete
- [ ] Team trained on usage
- [ ] Support documentation available

---

## Notes & Issues Found

| Issue # | Description | Status | Resolution |
|---------|-------------|--------|-----------|
| 1 | | | |
| 2 | | | |
| 3 | | | |

---

**Test Completion Date:** _______________  
**Total Issues Found:** _____  
**Critical Issues:** _____  
**Minor Issues:** _____  

**Status:** ☐ READY FOR PRODUCTION ☐ NEEDS FIXES ☐ IN PROGRESS

---

Last Updated: February 3, 2026  
Version: 1.0
