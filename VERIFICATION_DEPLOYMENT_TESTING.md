# ✅ PHOTOGRAPHER VERIFICATION - DEPLOYMENT & TESTING GUIDE

## 🚀 PRE-DEPLOYMENT CHECKLIST

### Code Quality ✅
- [x] All PHP files created and populated
- [x] All migrations created with proper schema
- [x] Models with relationships defined
- [x] Controllers with business logic complete
- [x] Policies with authorization gates
- [x] Form requests with validation
- [x] Notifications with queuing
- [x] Routes registered and middleware applied
- [x] Vue components created and styled
- [x] Badge component reusable

### Security ✅
- [x] File upload validation implemented
- [x] Secure private storage configured
- [x] Authorization policies in place
- [x] Form request validation on all inputs
- [x] Immutable audit trail setup
- [x] Admin-only operations protected
- [x] Foreign keys with cascading defined
- [x] Document download via signed routes

### Database ✅
- [x] 2 migrations created
- [x] verification_requests table with 25 fields
- [x] user_verifications table with 6 fields
- [x] Proper indexes on all query fields
- [x] Foreign key constraints defined
- [x] Enum types for status and level

---

## 📥 DEPLOYMENT STEPS

### Step 1: Run Migrations
```bash
cd "c:\xampp\htdocs\Photographar SB"
php artisan migrate --force
```

**Expected Output:**
- verification_requests table created
- user_verifications table created

### Step 2: Clear Application Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 3: Build Frontend Assets
```bash
npm run build
```

### Step 4: Configure Queue
Ensure `.env` has:
```
QUEUE_CONNECTION=database
```

### Step 5: Start Queue Worker
```bash
# Terminal 1: Production
php artisan queue:work

# Or for development
php artisan queue:work --sleep=3 --tries=1
```

### Step 6: Verify Routes
```bash
php artisan route:list | findstr verification
```

**Expected:** 10 routes should appear

### Step 7: Test File Storage
```bash
# Ensure private disk is writable
ls -la storage/app/private
chmod -R 755 storage/app/private
```

---

## 🧪 TESTING GUIDE

### Test 1: Photographer Submission

**Steps:**
1. Log in as photographer user
2. Navigate to `/verification`
3. Click "Start Verification"
4. Select verification type (e.g., "Phone Verification")
5. Fill in:
   - Full Name
   - Phone Number
   - Optional: NID or Business info
6. Upload documents (JPG/PNG/PDF)
7. Click "Submit Verification Request"

**Expected Results:**
- ✅ Form submits successfully
- ✅ Redirected to verification dashboard
- ✅ Status shows "Verification Pending"
- ✅ Photographer receives email notification
- ✅ Request appears in database

**Validation:**
```sql
SELECT * FROM verification_requests WHERE user_id = 123 ORDER BY created_at DESC LIMIT 1;
```

---

### Test 2: Admin Review Process

**Steps:**
1. Log in as admin user
2. Navigate to `/admin/verifications`
3. Should see pending requests table
4. Click "Review" on a pending request
5. Review displayed documents
6. Click "Approve" button
7. Enter optional admin note
8. Confirm approval

**Expected Results:**
- ✅ Request details display correctly
- ✅ Documents visible in preview
- ✅ Approval button functional
- ✅ Status changes to "approved"
- ✅ Photographer notified via email
- ✅ Verified badge now appears

**Validation:**
```sql
SELECT * FROM verification_requests WHERE id = 1;
SELECT * FROM user_verifications WHERE user_id = 123;
```

---

### Test 3: Badge Display

**Steps:**
1. Approve a photographer (from Test 2)
2. Navigate to photographer's profile
3. View photographer card
4. Search for photographers
5. View event mentors list

**Expected Results:**
- ✅ Green verified badge appears on profile
- ✅ Badge visible on photographer cards
- ✅ Badge visible in search results
- ✅ Verified photographers rank first
- ✅ Tooltip shows on badge hover

---

### Test 4: Rejection Workflow

**Steps:**
1. Log in as admin
2. Go to `/admin/verifications`
3. Click "Review" on a pending request
4. Click "Reject" button
5. Enter rejection reason (required)
6. Confirm rejection

**Expected Results:**
- ✅ Status changes to "rejected"
- ✅ Reason is stored
- ✅ Photographer receives rejection email with reason
- ✅ Photographer can submit new request
- ✅ Badge does not appear

**Validation:**
```sql
SELECT * FROM verification_requests WHERE id = 2;
```

---

### Test 5: File Upload Security

**Steps:**
1. Attempt to access document directly via URL
   - `/verification/{id}/download/front`
   - Without being owner/admin
2. Try uploading file >10MB
3. Try uploading unsupported file type (e.g., .exe)
4. Download as owner
5. Download as admin

**Expected Results:**
- ✅ Unauthorized access denied
- ✅ Large file rejected with error
- ✅ Invalid file type rejected
- ✅ Owner can download own files
- ✅ Admin can download all files
- ✅ Downloaded file is correct

---

### Test 6: Form Validation

**Steps:**
1. Go to `/verification/create`
2. Try submitting empty form
3. Submit with invalid phone
4. Submit with invalid email-like phone
5. Upload all fields correctly

**Expected Results:**
- ✅ Required field errors shown
- ✅ Phone validation error displayed
- ✅ File size validation shows
- ✅ File type validation shows
- ✅ Form submits successfully when valid

---

### Test 7: Admin Filtering

**Steps:**
1. Go to `/admin/verifications`
2. Filter by status: "pending"
3. Filter by type: "nid"
4. Search by photographer name
5. Clear filters

**Expected Results:**
- ✅ Results filtered correctly
- ✅ Only matching requests shown
- ✅ Search works across name/email/phone
- ✅ Multiple filters work together
- ✅ Clear resets all filters

---

### Test 8: Notifications

**Steps:**
1. Submit verification request
2. Check email for submission notification
3. Approve request as admin
4. Check email for approval notification
5. Reject another request
6. Check email for rejection with reason

**Expected Results:**
- ✅ All 3 notifications sent
- ✅ Emails arrive in inbox
- ✅ Correct information in each
- ✅ Links work properly
- ✅ Rejection reason visible

**Database Check:**
```sql
SELECT * FROM notifications WHERE notifiable_id = 123 ORDER BY created_at DESC;
```

---

### Test 9: Mobile Responsiveness

**Steps:**
1. Open `/verification` on mobile
2. Open `/verification/create` on mobile
3. Open admin list on mobile
4. Open admin detail on mobile
5. Try uploading document on mobile

**Expected Results:**
- ✅ All pages responsive
- ✅ Forms work on small screens
- ✅ Tables scrollable on mobile
- ✅ Buttons clickable
- ✅ File upload works

---

### Test 10: Ranking Boost

**Steps:**
1. Get list of all photographers
2. Verify a few photographers
3. Re-fetch photographer list
4. Compare order

**Expected Results:**
- ✅ Verified photographers appear first
- ✅ Verified sorted by rating
- ✅ Unverified appear after
- ✅ Order is consistent

---

## 🐛 TROUBLESHOOTING

### Issue: File Upload Fails
**Solution:**
```bash
# Check storage permissions
chmod -R 755 storage/app/private

# Check disk is configured in config/filesystems.php
# private disk should use local driver
```

### Issue: Notifications Not Sending
**Solution:**
```bash
# Check queue worker is running
php artisan queue:work

# Check notifications table
php artisan migrate

# Check MAIL_* in .env is configured
```

### Issue: Routes Not Found
**Solution:**
```bash
# Clear and re-register routes
php artisan route:clear
php artisan cache:clear

# Verify routes exist
php artisan route:list | findstr verification
```

### Issue: Badge Not Showing
**Solution:**
```php
// Ensure relationship is loaded in query
$photographer = Photographer::with('verification')->find($id);

// Check verification is actually set
dd($photographer->verification);
```

### Issue: Authorization Denied
**Solution:**
```php
// Check user role
dd(auth()->user()->role);

// Check policy is registered
php artisan policy --list
```

---

## 📊 POST-DEPLOYMENT VERIFICATION

```bash
# 1. Check migration status
php artisan migrate:status

# 2. Verify tables exist
mysql> SHOW TABLES LIKE '%verification%';

# 3. Check routes registered
php artisan route:list | grep verification

# 4. Test artisan commands
php artisan tinker
>>> VerificationRequest::count()
>>> UserVerification::count()

# 5. Queue status
php artisan queue:failed

# 6. Check logs
tail -f storage/logs/laravel.log
```

---

## ✅ SIGN-OFF CHECKLIST

- [ ] All migrations ran successfully
- [ ] All 10 routes available
- [ ] Photographer can submit requests
- [ ] Admin can approve/reject
- [ ] Verified badge displays correctly
- [ ] Notifications sent to email
- [ ] File uploads secure
- [ ] Filters and search work
- [ ] Mobile UI responsive
- [ ] Verified photographers rank first
- [ ] No errors in logs
- [ ] Database integrity verified
- [ ] Performance acceptable
- [ ] Documentation complete

---

## 🎉 PRODUCTION READY

Once all tests pass, the system is ready for production deployment!

**Key Endpoints to Monitor:**
- `/verification` - Photographer dashboard
- `/verification/create` - Submission form
- `/admin/verifications` - Admin panel
- Queue worker status
- File storage usage

**Support Resources:**
- See VERIFICATION_SYSTEM_COMPLETE.md for full documentation
- Check migrations in database/migrations/
- Review models in app/Models/
- Check controllers for business logic
