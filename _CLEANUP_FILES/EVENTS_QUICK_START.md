# 🚀 Events Module - Quick Start Guide

## Ready for Testing? START HERE

---

## ⚡ Quick Setup (2 minutes)

```bash
# 1. Navigate to project
cd "C:\xampp\htdocs\Photographar SB"

# 2. Start Laravel dev server
php artisan serve

# 3. In another terminal, verify environment
php verify-browser-testing.php
```

**Result:** Should show "STATUS: READY FOR BROWSER TESTING ✅"

---

## 🎯 First Test (5 minutes)

### Step 1: Open Browser
```
http://localhost:8000/events
```

**What You Should See:**
- Events page with title "Events"
- List/Grid view toggle
- 1-2 events displayed (from database)
- Filter sidebar (city, date range, price, sort)
- Responsive design on your screen size

**If Error:** Check `php artisan log` for database errors

---

### Step 2: Quick Registration Test
1. Click on any event
2. Click "Confirm Registration" (free event) or "Proceed to Payment" (paid)
3. If not logged in → Login first
4. Confirm registration
5. You should see a confirmation page with QR code

**Expected Output:**
- ✅ Registration code displays (e.g., REG-XXXXXXXX)
- ✅ QR code visible
- ✅ Success message
- ✅ Event details shown

---

### Step 3: Check Admin Panel
1. Go to: `http://localhost:8000/admin/events`
2. Login as admin if prompted
3. See list of all events
4. Click on one event
5. Click "Attendance" or "Check-in"

**What You Should See:**
- ✅ Event details
- ✅ Attendance stats (Attended, Registered, Rate)
- ✅ Manual entry field for registration codes
- ✅ Recent check-ins list

---

## 📋 Full Testing Checklist

**File:** [EVENTS_BROWSER_TESTING_CHECKLIST.md](EVENTS_BROWSER_TESTING_CHECKLIST.md)

**Contains:** 20 comprehensive tests across:
- ✅ Public event browsing (3 tests)
- ✅ Registration flow (2 tests)
- ✅ User dashboard (1 test)
- ✅ Admin CRUD (3 tests)
- ✅ Attendance scanning (2 tests)
- ✅ Certificates (3 tests)
- ✅ API endpoints (4 tests)
- ✅ Error handling & performance (2 tests)

**Time to Complete:** 30-45 minutes

---

## 🔍 Key Features to Test

### 1. Event Browsing ✅
- [ ] Events list loads
- [ ] Search/filter work
- [ ] Pagination works
- [ ] Responsive on mobile

### 2. Registration ✅
- [ ] Free event registration works
- [ ] Confirmation page shows QR
- [ ] QR code visible or "generating" message
- [ ] Registration code matches

### 3. Attendance Tracking ✅
- [ ] Enter registration code in attendance form
- [ ] "Check-in" succeeds
- [ ] Success message appears
- [ ] Stats update

### 4. Certificates ✅
- [ ] Certificate created after check-in
- [ ] Shows in user dashboard
- [ ] Download works (if implemented)

### 5. Admin Features ✅
- [ ] Admin can see all events
- [ ] Admin can create/edit events
- [ ] Admin can view attendance
- [ ] Admin can check in users

---

## 🛠️ If Something Breaks

### Issue: "404 Not Found"
**Solution:**
```bash
php artisan route:cache --clear
php artisan cache:clear
php artisan view:clear
```

### Issue: "QR Code Not Generating"
**Check:**
```bash
# Verify QR directory exists
dir storage\app\public\qr-codes\registrations

# Check storage link
dir public\storage
```

### Issue: Database Errors
**Solution:**
```bash
# Run migrations
php artisan migrate

# Verify tables
php artisan tinker
>>> \App\Models\Event::count()
>>> \App\Models\EventRegistration::count()
```

### Issue: "Cannot login"
**Check:**
```bash
# List admin users
php artisan tinker
>>> \App\Models\User::where('role', 'admin')->get()

# If none exist, create one
>>> \App\Models\User::factory()->create(['role' => 'admin'])
```

---

## 📊 System Status

### Environment
- ✅ APP_ENV: local
- ✅ APP_DEBUG: true
- ✅ Database: Connected (73 tables)
- ✅ Routes: 21 event routes registered
- ✅ Storage: Configured (public/storage symlink)

### Data
- ✅ Events: 2 in database
- ✅ Registrations: 1 in database
- ✅ Certificates: 1 auto-issued
- ✅ Users: 35 total, 2 admin

### Ready for Testing
- ✅ All core features implemented
- ✅ Database migrated
- ✅ Routes registered
- ✅ Storage configured
- ✅ QR generation working
- ✅ Certificate system working

---

## 🎯 Test URLs (Quick Reference)

| Feature | URL |
|---------|-----|
| Browse Events | http://localhost:8000/events |
| My Registrations | http://localhost:8000/my-registrations |
| Admin Events | http://localhost:8000/admin/events |
| API Events | http://localhost:8000/api/v1/events |
| Attendance | http://localhost:8000/admin/events/{id}/attendance |
| Mobile Scanner | http://localhost:8000/admin/events/{id}/attendance/mobile |

Replace `{id}` with actual event ID from database.

---

## ✅ Testing Workflow

```
1. Start Server (php artisan serve)
   ↓
2. Verify Environment (php verify-browser-testing.php)
   ↓
3. Open http://localhost:8000/events
   ↓
4. Follow EVENTS_BROWSER_TESTING_CHECKLIST.md (20 tests)
   ↓
5. Record results
   ↓
6. Report any issues
   ↓
7. Deploy to production!
```

---

## 📈 Success Criteria

✅ **All 20 tests pass** → System ready for production
🟡 **15-19 tests pass** → Minor issues to fix
🔴 **< 15 tests pass** → Critical issues to resolve

---

## 📞 Need Help?

**Reference Files:**
- [EVENTS_DEPLOYMENT_GUIDE.md](EVENTS_DEPLOYMENT_GUIDE.md) - Deployment instructions
- [EVENTS_BROWSER_TESTING_CHECKLIST.md](EVENTS_BROWSER_TESTING_CHECKLIST.md) - Detailed test steps
- [EVENTS_TESTING_COMPLETE.md](EVENTS_TESTING_COMPLETE.md) - Test results

**Documentation:**
- [P1_EVENTS_MODULE_COMPLETE.md](P1_EVENTS_MODULE_COMPLETE.md) - Complete implementation details

---

## 🚀 Next Steps After Testing

1. ✅ **Pass all 20 browser tests**
2. ⏳ **Fix any issues found**
3. ⏳ **Install SSL certificate** (for HTTPS/mobile camera)
4. ⏳ **Configure .env** for production
5. ⏳ **Deploy to production server**
6. 🎉 **Launch!**

---

**Ready to test?** Open: `http://localhost:8000/events`

**Issues?** Check the [checklist](EVENTS_BROWSER_TESTING_CHECKLIST.md) or [deployment guide](EVENTS_DEPLOYMENT_GUIDE.md)

**Estimated Testing Time:** 30-45 minutes  
**System Status:** 🟢 READY  
**Last Updated:** February 4, 2026
