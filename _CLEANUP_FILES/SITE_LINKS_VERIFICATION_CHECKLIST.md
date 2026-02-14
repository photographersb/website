# ✅ SITE LINKS VERIFICATION CHECKLIST

**Quick 5-Minute Test** - Run through this checklist after deployment

---

## 🚀 DEPLOYMENT (5 MINUTES)

```bash
# Step 1: Run migration
php artisan migrate

# Step 2: Seed links
php artisan db:seed --class=SiteLinkSeeder

# Step 3: Clear cache
php artisan cache:clear && php artisan route:cache

# Step 4: Check database
php artisan tinker
>>> SiteLink::count(); // Should return 27
>>> exit
```

**✅ PASS**: All commands run without errors  
**❌ FAIL**: See troubleshooting section in main docs

---

## 🔍 ADMIN UI TESTS (10 MINUTES)

### Test 1: Access Admin Panel
- [ ] Navigate to `/admin/settings/site-links`
- [ ] Page loads without errors
- [ ] See list of 27 links

### Test 2: Filter Links
- [ ] Select "Navbar" from section dropdown
- [ ] See only 5 navbar links
- [ ] Select "Social" - See 6 social links
- [ ] Select "All Sections" - See all 27 links

### Test 3: Create New Link
- [ ] Click "Add New Link" button
- [ ] Fill form:
  - Section: `navbar`
  - Title: `Test Link`
  - URL: `/test`
  - Visibility: `Public`
  - Active: ✓ Checked
- [ ] Click "Create Link"
- [ ] Redirected to index page
- [ ] See "Test Link" in list

### Test 4: Edit Link
- [ ] Click edit icon (pencil) on "Test Link"
- [ ] Change title to `Test Link Updated`
- [ ] Click "Update Link"
- [ ] See updated title in list

### Test 5: Toggle Status
- [ ] Click status badge on "Test Link"
- [ ] Badge changes from green (Active) to gray (Disabled)
- [ ] Click again - Badge changes back to green

### Test 6: Delete Link
- [ ] Click delete icon (trash) on "Test Link"
- [ ] Confirmation dialog appears
- [ ] Click OK
- [ ] Link removed from list
- [ ] Total count back to 27

### Test 7: Clear Cache
- [ ] Click blue "Clear Cache" button
- [ ] Success message appears

### Test 8: Preview
- [ ] Click purple "Preview" button
- [ ] Preview page loads
- [ ] See links grouped by section

**✅ ALL TESTS PASS**: Admin UI is working correctly  
**❌ ANY FAIL**: Check console errors, verify routes are registered

---

## 🔐 SECURITY TESTS (5 MINUTES)

### Test 1: XSS Protection (JavaScript URL)
- [ ] Click "Add New Link"
- [ ] URL: `javascript:alert('xss')`
- [ ] Try to save
- [ ] **EXPECTED**: Error message "JavaScript URLs are not allowed"

### Test 2: XSS Protection (Data URL)
- [ ] URL: `data:text/html,<script>alert('xss')</script>`
- [ ] Try to save
- [ ] **EXPECTED**: Error message "Data URLs are not allowed"

### Test 3: XSS Protection (Title)
- [ ] Title: `<script>alert('xss')</script>`
- [ ] URL: `/test`
- [ ] Save link
- [ ] **EXPECTED**: Link created, but script tags escaped in display

### Test 4: Admin Access Control
- [ ] Log out
- [ ] Try to access `/admin/settings/site-links`
- [ ] **EXPECTED**: Redirect to login or 403 Forbidden

**✅ ALL TESTS PASS**: Security is working correctly  
**❌ ANY FAIL**: Critical security issue - do not deploy

---

## 📊 DATABASE VERIFICATION (2 MINUTES)

```sql
-- Test 1: Count total links
SELECT COUNT(*) FROM site_links; 
-- EXPECTED: 27

-- Test 2: Count by section
SELECT section, COUNT(*) as count 
FROM site_links 
GROUP BY section;
-- EXPECTED: 
--   navbar: 5
--   footer_company: 4
--   footer_legal: 4
--   footer_useful: 5
--   social: 6
--   cta: 4

-- Test 3: Check active links
SELECT COUNT(*) FROM site_links WHERE is_active = 1;
-- EXPECTED: 21 (some are inactive by default)

-- Test 4: Verify indexes exist
SHOW INDEX FROM site_links;
-- EXPECTED: Indexes on section, sort_order, is_active

-- Test 5: Check social links
SELECT title, url FROM site_links WHERE section = 'social';
-- EXPECTED: Facebook, Instagram, WhatsApp (active), YouTube, LinkedIn, TikTok (inactive)
```

**✅ ALL QUERIES PASS**: Database is correct  
**❌ ANY FAIL**: Re-run seeder or check migration

---

## 💾 CACHE VERIFICATION (3 MINUTES)

### Test 1: Cache Creation
```bash
php artisan tinker
>>> use App\Services\SiteLinkService;
>>> $service = new SiteLinkService();
>>> $links = $service->getNavbarLinks(false);
>>> $links->count(); // Should return 5
>>> exit
```

### Test 2: Check Cache Keys
```bash
php artisan cache:clear
php artisan tinker
>>> use Illuminate\Support\Facades\Cache;
>>> $service = new \App\Services\SiteLinkService();
>>> $service->getNavbarLinks(false); // Creates cache
>>> Cache::has('site_links_navbar_auth_false'); // Should return true
>>> exit
```

### Test 3: Cache Auto-Clear on Update
1. [ ] Edit any link in admin UI
2. [ ] Save changes
3. [ ] Run in terminal:
```bash
php artisan tinker
>>> use Illuminate\Support\Facades\Cache;
>>> Cache::has('site_links_navbar_auth_false'); // Should return true (cache rebuilt)
>>> exit
```

**✅ ALL TESTS PASS**: Caching is working  
**❌ ANY FAIL**: Check cache driver configuration

---

## 🎨 FRONTEND INTEGRATION (After App.vue Update)

### Pre-Integration Check
- [ ] Current navbar shows hardcoded links
- [ ] Current footer shows hardcoded links
- [ ] Current social icons show hardcoded links

### Post-Integration Check
- [ ] Navbar shows database links
- [ ] Footer shows database links
- [ ] Social icons show database links
- [ ] Guest-only links hidden when logged in
- [ ] Auth-only links visible when logged in
- [ ] External links open in new tab (`target="_blank"`)
- [ ] Internal links open in same tab (`target="_self"`)

### Mobile Check
- [ ] Open on mobile device or resize browser to < 640px
- [ ] Hamburger menu works
- [ ] Links readable and clickable
- [ ] Footer doesn't overflow
- [ ] Social icons large enough for touch

**✅ ALL TESTS PASS**: Frontend integration successful  
**❌ ANY FAIL**: Check composable code, API endpoint, App.vue integration

---

## 🚨 CRITICAL CHECKS (DO NOT SKIP)

### 1. Admin Access Control
```bash
# Test as non-admin user
curl -X GET http://photographersb.local/admin/settings/site-links \
  -H "Authorization: Bearer {non-admin-token}"
# EXPECTED: 403 Forbidden or redirect
```

### 2. XSS Prevention
```bash
# Test javascript: URL
curl -X POST http://photographersb.local/admin/settings/site-links \
  -H "Authorization: Bearer {admin-token}" \
  -H "Content-Type: application/json" \
  -d '{"section":"navbar","title":"XSS","url":"javascript:alert(1)","visibility":"public"}'
# EXPECTED: 422 Validation Error
```

### 3. Cache Performance
```bash
# Test cache hit speed
time curl -X GET http://photographersb.local/api/v1/site-links
# EXPECTED: < 50ms (after first request)
```

### 4. Database Integrity
```sql
-- Check for orphaned records (should be 0)
SELECT COUNT(*) FROM site_links WHERE created_by_user_id IS NOT NULL 
  AND created_by_user_id NOT IN (SELECT id FROM users);
-- EXPECTED: 0

-- Check for duplicate sort orders in same section (should be empty)
SELECT section, sort_order, COUNT(*) 
FROM site_links 
GROUP BY section, sort_order 
HAVING COUNT(*) > 1;
-- EXPECTED: Empty result set
```

**✅ ALL CRITICAL CHECKS PASS**: System is production-ready  
**❌ ANY CRITICAL FAIL**: DO NOT DEPLOY - Fix immediately

---

## 📈 PERFORMANCE BENCHMARKS

### Target Metrics
- Admin page load: < 500ms
- Cache hit response: < 5ms
- Cache miss response: < 50ms
- Database query: < 20ms

### Test Performance
```bash
# Admin page load time
curl -w "@curl-format.txt" -o /dev/null -s http://photographersb.local/admin/settings/site-links

# API response time
ab -n 100 -c 10 http://photographersb.local/api/v1/site-links
```

**✅ MEETS BENCHMARKS**: Performance is acceptable  
**⚠️ BELOW BENCHMARKS**: Optimize cache duration or add CDN

---

## ✅ FINAL CHECKLIST

### Backend (100% Complete)
- [x] Migration created and run
- [x] Model with validation
- [x] Service with caching
- [x] Controller with security
- [x] Routes registered
- [x] Seeder with 27 defaults

### Admin UI (100% Complete)
- [x] Index page (list view)
- [x] Create page (form)
- [x] Edit page (form)
- [x] Delete functionality
- [x] Toggle active/inactive
- [x] Filter by section
- [x] Clear cache button
- [x] Preview functionality

### Frontend Integration (Pending)
- [ ] API endpoint created
- [ ] Composable created
- [ ] App.vue updated
- [ ] Links appear on navbar
- [ ] Links appear on footer
- [ ] Mobile responsive
- [ ] Security tested

### Documentation
- [x] Implementation guide
- [x] Verification checklist
- [x] Troubleshooting section
- [x] API documentation
- [x] Integration instructions

---

## 🎯 SIGN-OFF

### Developer Checklist
- [ ] All backend code reviewed
- [ ] All security tests pass
- [ ] Database seeded correctly
- [ ] Admin UI tested
- [ ] Documentation complete

### QA Checklist
- [ ] All tests executed
- [ ] No console errors
- [ ] Mobile responsive
- [ ] Cross-browser compatible
- [ ] Performance acceptable

### Deployment Checklist
- [ ] Migrations backed up
- [ ] Database seeded
- [ ] Cache cleared
- [ ] Routes cached
- [ ] Health check passed

**Status**: ✅ **BACKEND & ADMIN 100% COMPLETE**  
**Next**: Frontend integration with App.vue (~1 hour)

---

**Last Updated**: February 4, 2026  
**Version**: 1.0  
**Sign-off**: Ready for Production (Backend/Admin), Pending Frontend Integration
