# Admin Error Center - Verification Test Checklist

**Date:** 2026-02-03  
**Status:** Production Ready  
**Version:** 1.0

---

## A) DATABASE & MODELS

### A.1 - Check Migrations
- [ ] Run: `php artisan migrate`
- [ ] Verify tables exist:
  ```bash
  php artisan tinker
  > DB::table('admin_error_logs')->count()
  > DB::table('admin_error_log_notes')->count()
  ```

### A.2 - Verify Table Structure
```bash
php artisan tinker
> Schema::getColumns('admin_error_logs')
```
**Expected columns:**
- ✓ id, severity, environment, url, route_name, method, status_code
- ✓ user_id, ip, user_agent, message, exception_class, file, line
- ✓ trace, signature_hash, occurrences, first_seen_at, last_seen_at
- ✓ is_muted, is_resolved, resolved_by_user_id, resolved_at
- ✓ created_at, updated_at

### A.3 - Test Models
```bash
php artisan tinker

# Create test error log
$error = App\Models\AdminErrorLog::create([
    'severity' => 'P1',
    'environment' => 'local',
    'message' => 'Test error message',
    'exception_class' => 'TestException',
    'file' => '/app/Test.php',
    'line' => 123,
    'signature_hash' => hash('sha256', 'test'),
    'occurrences' => 1,
    'user_id' => 1,
]);

# Verify relationships
$error->load('user', 'resolvedByUser', 'notes');

# Verify model methods
$error->markResolved(auth()->user());
$error->markUnresolved();
$error->mute();
$error->unmute();
```

---

## B) ERROR CAPTURE INTEGRATION

### B.1 - Test Exception Handler
```bash
php artisan tinker

# Test basic exception
try {
    throw new Exception('Test error message');
} catch (Exception $e) {
    app(App\Services\ErrorLogService::class)->logException($e);
}

# Verify it was logged
$error = DB::table('admin_error_logs')->latest()->first();
dd($error);
```

### B.2 - Test Signature Deduplication
1. Trigger same exception twice in browser:
   ```
   http://localhost/test-error
   http://localhost/test-error
   ```
   
2. Check database:
   ```bash
   php artisan tinker
   > App\Models\AdminErrorLog::where('signature_hash', ...)->get()
   ```
   
3. **Verify:**
   - ✓ Only ONE database record created
   - ✓ `occurrences` count = 2
   - ✓ `last_seen_at` updated

### B.3 - Test Severity Detection
Trigger different exceptions:
```bash
# P0: Critical
http://localhost/test-error-critical

# P1: High priority
http://localhost/test-error-auth

# P2: Medium
http://localhost/test-error-not-found
```

**Verify in database:**
- ✓ PDOException = P0
- ✓ AuthenticationException = P1
- ✓ ModelNotFoundException = P2

### B.4 - Test Sensitive Data Sanitization
1. Trigger an error with sensitive data:
   ```php
   throw new Exception('API Key: sk_test_4eC39HqLyjWDarht...');
   ```

2. Check database:
   ```bash
   php artisan tinker
   > $error = App\Models\AdminErrorLog::latest()->first();
   > dd($error->message);
   ```

3. **Verify:**
   - ✓ Message contains `api_key=***` instead of actual key
   - ✓ No passwords logged
   - ✓ No tokens logged

---

## C) ERROR CENTER ADMIN UI

### C.1 - Test Dashboard Access
1. **Login as admin:** `/admin`
2. **Navigate to:** Admin → System Health → Error Center (or `/admin/system-health/errors`)
3. **Verify:**
   - ✓ Page loads without errors
   - ✓ Stats cards display (Errors Today, Open Errors, P0 Errors, Muted)
   - ✓ Stats are accurate (count rows in database)
   - ✓ Error table displays all errors

### C.2 - Test Filters
Apply each filter individually:
- [ ] Filter by Severity (P0, P1, P2)
- [ ] Filter by Status (Open, Resolved, Muted)
- [ ] Filter by Environment (local, production)
- [ ] Filter by Status Code (500, 404, etc.)
- [ ] Search by message
- [ ] Search by URL
- [ ] Search by Route name
- [ ] Clear filters button resets all

### C.3 - Test Pagination
- [ ] Paginate through error list (25 per page)
- [ ] Pagination links work
- [ ] "Next" and "Previous" buttons work

### C.4 - Test Error Detail Page
1. Click "View" on any error row
2. **Verify:**
   - ✓ Error message displays
   - ✓ Severity badge shows
   - ✓ Status badge shows
   - ✓ Exception class displays
   - ✓ File and line number show
   - ✓ URL is clickable
   - ✓ Request info (method, IP, user agent) displays
   - ✓ Statistics show occurrences count
   - ✓ Signature hash visible

### C.5 - Test Stack Trace (Super Admin Only)
1. Login as super_admin
2. Open error detail page
3. **Verify:**
   - ✓ Stack trace section visible
   - ✓ Trace is formatted JSON
   - ✓ Trace shows file paths, line numbers, functions

4. Login as regular admin
5. Open same error detail page
6. **Verify:**
   - ✓ Stack trace section hidden or redacted

---

## D) SINGLE-CLICK ACTIONS

### D.1 - Test Resolve Action
1. Open error detail page for an open error
2. Click "Resolve" button
3. **Verify:**
   - ✓ Page refreshes
   - ✓ Status badge changes to "✅ Resolved"
   - ✓ "Resolve" button changes to "Reopen"
   - ✓ Database: `is_resolved = true`, `resolved_by_user_id` set, `resolved_at` set
   - ✓ Back in dashboard, error shows as "Resolved"

### D.2 - Test Reopen Action
1. Click "Reopen" on a resolved error
2. **Verify:**
   - ✓ Page refreshes
   - ✓ Status badge changes to "⚠️ Open"
   - ✓ Resolve button reappears
   - ✓ Database: `is_resolved = false`, `resolved_by_user_id = null`, `resolved_at = null`

### D.3 - Test Mute Action
1. Open an open error with similar errors
2. Click "Mute" button
3. **Verify:**
   - ✓ Success message shows
   - ✓ Page refreshes
   - ✓ Status badge changes to "🔇 Muted"
   - ✓ Database: ALL errors with same `signature_hash` have `is_muted = true`
   - ✓ Dashboard: all similar errors show "Muted" status
   - ✓ "Unmute" button appears

### D.4 - Test AJAX Response Format
Open browser DevTools → Network tab:
1. Click Resolve button
2. Check network request:
   - ✓ POST to `/api/v1/admin/system-health/errors/{id}/resolve`
   - ✓ Response: `{ "success": true, "message": "...", "error": {...} }`

---

## E) ERROR NOTES FEATURE

### E.1 - Test Adding Note
1. On error detail page, scroll to "Notes" section
2. Type note: "Investigating this issue"
3. Click "Add Note"
4. **Verify:**
   - ✓ Note appears in list immediately
   - ✓ Shows your username/email
   - ✓ Shows timestamp
   - ✓ Database: entry in `admin_error_log_notes`

### E.2 - Test Multiple Notes
1. Add 2-3 more notes
2. **Verify:**
   - ✓ All notes display in order (newest first)
   - ✓ Each note shows correct author and timestamp
   - ✓ Notes persist after page refresh

---

## F) SECURITY & AUTHORIZATION

### F.1 - Test Role-Based Access
1. **As guest/unauthenticated:**
   - [ ] Try to access `/admin/system-health/errors` → Redirect to login
   - [ ] Try to access API `/api/v1/admin/system-health/errors` → 401 Unauthorized

2. **As regular user (not admin):**
   - [ ] Try to access `/admin/system-health/errors` → 403 Forbidden
   - [ ] Try to access error detail → 403 Forbidden

3. **As admin:**
   - [ ] Can access dashboard ✓
   - [ ] Can view errors ✓
   - [ ] Can resolve/reopen errors ✓
   - [ ] Cannot view stack trace (see redacted message) ✓

4. **As super_admin:**
   - [ ] Can access dashboard ✓
   - [ ] Can view all errors ✓
   - [ ] CAN view stack trace ✓
   - [ ] Can export to CSV ✓

### F.2 - Test CSRF Protection
1. In browser DevTools, check requests
2. Verify each POST request includes `X-CSRF-Token` header
3. **Verify:**
   - ✓ Token is present and valid
   - ✓ POST without token fails (403)

---

## G) ERROR CAPTURE SCENARIOS

### G.1 - Test Database Connection Error
1. Stop MySQL/Database
2. Try to access any page that queries database
3. **Verify:**
   - ✓ Error logged as P0
   - ✓ Appears in Error Center immediately
   - ✓ Dashboard shows 1 P0 error

### G.2 - Test 500 Server Error
1. Intentionally cause PHP error:
   ```php
   Route::get('/test-error', function () {
       throw new Exception('Test error');
   });
   ```

2. Visit http://localhost/test-error
3. **Verify:**
   - ✓ Error page displays
   - ✓ Error logged to Error Center
   - ✓ Dashboard shows new error

### G.3 - Test Excluded Errors (NOT Logged)
These should NOT appear in Error Center:

1. **404 errors (production only):**
   ```
   http://localhost/nonexistent-page
   ```
   - ✓ Not logged in production

2. **Validation errors:**
   ```
   POST /api/users with invalid data
   ```
   - ✓ Not logged (expected behavior)

3. **Auth exceptions:**
   ```
   http://localhost/protected-page (without auth)
   ```
   - ✓ Not logged (expected behavior)

### G.4 - Test Error Suppression (Mute)
1. Trigger error A, B, C (same signature)
2. Mute error A
3. **Verify:**
   - ✓ Error A muted
   - ✓ Error B muted (same signature)
   - ✓ Error C muted (same signature)
4. Trigger same error again
5. **Verify:**
   - ✓ New error not logged (muted signature)

---

## H) PERFORMANCE & LOAD TESTING

### H.1 - Test Dashboard Performance
1. Generate 1000+ error logs in database
2. Open Error Center dashboard
3. **Verify:**
   - ✓ Page loads < 3 seconds
   - ✓ Pagination works smoothly
   - ✓ Filters apply quickly
   - ✓ Stats cards calculate quickly

### H.2 - Test Large Trace Performance
1. Create error with deep stack trace (50+ frames)
2. View detail page
3. **Verify:**
   - ✓ Page loads smoothly
   - ✓ Trace section renders without lag
   - ✓ JSON formatting doesn't block UI

### H.3 - Test Export Performance
1. Select 500+ errors with filters
2. Click "Export to CSV"
3. **Verify:**
   - ✓ Export completes < 10 seconds
   - ✓ CSV file downloads correctly
   - ✓ CSV contains all filtered errors

---

## I) DATA INTEGRITY

### I.1 - Test Duplicate Error Handling
1. Trigger identical exception 10 times
2. Check database:
   ```bash
   php artisan tinker
   > App\Models\AdminErrorLog::count()  # Should be 1, not 10
   > App\Models\AdminErrorLog::first()->occurrences  # Should be 10
   ```
3. **Verify:**
   - ✓ Only 1 database record
   - ✓ `occurrences` = 10
   - ✓ Timestamps updated

### I.2 - Test Orphaned Records Cleanup
1. Create error log
2. Delete related user
3. **Verify:**
   - [ ] Error still appears in dashboard (user_id = null is handled)
   - [ ] No database errors

### I.3 - Test Cascade Delete
1. Create error with multiple notes
2. Delete error: `AdminErrorLog::first()->delete()`
3. **Verify:**
   - [ ] Notes are cascade deleted
   - [ ] No orphaned notes in database

---

## J) ENVIRONMENT-SPECIFIC TESTING

### J.1 - Test Local Environment
1. Set `APP_ENV=local` in `.env`
2. Trigger 404 error
3. **Verify:**
   - ✓ 404 IS logged (debug mode)
   - ✓ Full trace is visible

### J.2 - Test Production Environment
1. Set `APP_ENV=production` in `.env`
2. Trigger 404 error
3. **Verify:**
   - ✓ 404 NOT logged (noise reduction)

### J.3 - Test Staging Environment
1. Set `APP_ENV=staging` in `.env`
2. Trigger error
3. **Verify:**
   - ✓ Error logged with environment = 'staging'
   - ✓ Shows as separate environment in dashboard

---

## K) API ENDPOINT TESTING

### K.1 - Test List Endpoint
```bash
GET /api/v1/admin/system-health/errors

# Verify response:
{
    "status": "success",
    "data": [...],
    "pagination": {...}
}
```
- [ ] Returns paginated list
- [ ] Filters work via query params
- [ ] Authorization check

### K.2 - Test Detail Endpoint
```bash
GET /api/v1/admin/system-health/errors/{id}

# Verify response:
{
    "status": "success",
    "data": {id, severity, message, ...}
}
```
- [ ] Returns single error
- [ ] Super admin sees trace
- [ ] Regular admin sees redacted trace

### K.3 - Test Action Endpoints
```bash
POST /api/v1/admin/system-health/errors/{id}/resolve
POST /api/v1/admin/system-health/errors/{id}/reopen
POST /api/v1/admin/system-health/errors/{id}/mute
POST /api/v1/admin/system-health/errors/{id}/unmute
```
- [ ] All return `{ "success": true, "message": "..." }`
- [ ] Database updated correctly

### K.4 - Test Export Endpoint
```bash
GET /api/v1/admin/system-health/errors/export
```
- [ ] Returns CSV file
- [ ] Filename includes timestamp
- [ ] Contains all columns

---

## L) EDGE CASES

### L.1 - Test Null Values
1. Trigger error with minimal info (no user, no route, etc.)
2. **Verify:**
   - ✓ Dashboard displays "N/A" instead of null
   - ✓ Detail page handles null gracefully
   - ✓ No database errors

### L.2 - Test Long Messages
1. Create error with 10,000 character message
2. **Verify:**
   - ✓ Message truncated in database (max 500 chars by service)
   - ✓ Dashboard displays truncated text
   - ✓ Full message in detail view

### L.3 - Test Special Characters
1. Trigger error with Unicode/emoji in message:
   ```
   throw new Exception('测试 🚀 Error: テスト');
   ```
2. **Verify:**
   - ✓ Displays correctly in dashboard
   - ✓ Displays correctly in detail page
   - ✓ Database stores correctly

### L.4 - Test Concurrent Errors
1. Simulate 10 concurrent errors
2. **Verify:**
   - ✓ All logged without conflicts
   - ✓ No race conditions
   - ✓ Occurrences count accurate

---

## M) PRODUCTION CHECKLIST

Before deploying to production:

- [ ] All migrations run successfully
- [ ] Database indexes created
- [ ] ErrorLogService properly sanitizes sensitive data
- [ ] Handler.php configured correctly
- [ ] Admin routes protected with auth middleware
- [ ] Super admin role verified in database
- [ ] Error logs not stored in version control
- [ ] Cron job setup for cleanup (optional): `php artisan schedule:work`
- [ ] Backup strategy for admin_error_logs table
- [ ] Monitoring/alerts for P0 errors configured
- [ ] Email notifications tested for P0 errors
- [ ] Stack trace only visible to super_admin
- [ ] CSV export accessible only to super_admin
- [ ] Rate limiting on error logging (no DoS)
- [ ] Performance baseline established

---

## N) MONITORING & MAINTENANCE

### N.1 - Daily Tasks
```bash
# Check for P0 errors
php artisan tinker
> App\Models\AdminErrorLog::where('severity', 'P0')
    ->where('is_resolved', false)
    ->count()
```

### N.2 - Weekly Tasks
```bash
# Clean old resolved errors (90+ days)
php artisan tinker
> app(App\Services\ErrorLogService::class)->cleanOldErrors(90);
```

### N.3 - Monthly Tasks
- [ ] Review error trends
- [ ] Check for patterns
- [ ] Update mute rules if needed
- [ ] Export error logs for archive

---

## O) SIGN-OFF CHECKLIST

**Testing completed by:** ____________  
**Date:** ____________  
**Environment:** ☐ Local ☐ Staging ☐ Production  

### Final Verification
- [ ] All tests in sections A-N passed
- [ ] No critical bugs found
- [ ] Performance acceptable
- [ ] Security validated
- [ ] Documentation complete
- [ ] Backup & recovery plan documented
- [ ] Team trained on Error Center usage

### Ready for Production?
**☐ YES - All tests passed, ready to deploy**  
**☐ NO - Issues found, see notes below:**

---

**Issues Found:**
```
1. ...
2. ...
3. ...
```

**Resolution:**
```
...
```

---

**Deployed by:** ____________  
**Deployment Date:** ____________  
**Build Version:** ____________  

---

## Documentation References

- ErrorLogService: `app/Services/ErrorLogService.php`
- Handler: `app/Exceptions/Handler.php`
- Controller: `app/Http/Controllers/Admin/AdminErrorCenterController.php`
- Models: `app/Models/AdminErrorLog.php`, `AdminErrorLogNote.php`
- Views: `resources/views/admin/error-center/`
- API Routes: `routes/api.php` (lines 678-686)

---

**Status:** PRODUCTION READY ✅
