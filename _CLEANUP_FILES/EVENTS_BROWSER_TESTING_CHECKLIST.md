# 🧪 Events Module - Browser Testing Checklist

## Pre-Testing Setup

### Environment Check
- [ ] Run: `php artisan migrate:fresh` (or skip if keeping test data)
- [ ] Run: `php artisan db:seed --class=EventSeeder` (optional - create more test events)
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Start Laravel server: `php artisan serve`
- [ ] Browser: Open `http://localhost:8000`
- [ ] Login as admin account (if required)

---

## Public User Flow Tests

### Test 1: Browse Events List
**URL:** `http://localhost:8000/events`
- [ ] Page loads without errors
- [ ] Event cards display correctly (title, price, date, capacity)
- [ ] Images load properly (if uploaded)
- [ ] No console JavaScript errors

**Search & Filter:**
- [ ] Search by event title works
- [ ] Filter by city dropdown functions
- [ ] Filter by type (free/paid) works
- [ ] Filter by date range works
- [ ] Sort options change order correctly
- [ ] Grid/List view toggle works

**Pagination:**
- [ ] Page 1 shows 12 events (or fewer if less exist)
- [ ] Next button navigates to page 2
- [ ] Prev button works on page 2
- [ ] Total count displays correctly

---

### Test 2: View Event Details
**URL:** `http://localhost:8000/events/{slug}`
- [ ] Page loads with full event information
- [ ] Title, description display correctly
- [ ] Banner image shows
- [ ] Date/Time formatted properly
- [ ] Capacity bar displays
- [ ] Location/Venue shows
- [ ] Registration button visible

**If Mentors Assigned:**
- [ ] Mentors section visible
- [ ] Mentor names/images display
- [ ] Mentor count shows

**If Paid Event:**
- [ ] Price displays (e.g., "৳500")
- [ ] "Proceed to Payment" button shows
- [ ] Button enabled (not grayed out)

**If Free Event:**
- [ ] Price shows "Free"
- [ ] "Confirm Registration" button shows
- [ ] Button enabled

---

### Test 3: Free Event Registration
**Starting Point:** Any free event details page
- [ ] Click "Confirm Registration" button
- [ ] If not logged in → Redirect to login, then back to registration
- [ ] If logged in → Immediate registration
- [ ] Success message displays
- [ ] Redirect to confirmation page

**Confirmation Page Checks:**
- [ ] Event details display
- [ ] QR code visible (or "generating..." message)
- [ ] Registration code displayed
- [ ] Download ticket link available
- [ ] Clear success message
- [ ] "Back to Events" button works

**Database Verification:**
- Run: `php artisan tinker`
- `>>> $reg = \App\Models\EventRegistration::latest()->first();`
- `>>> $reg->registration_code` - Should match screen
- `>>> $reg->payment_status` - Should be "free"
- `>>> $reg->attended_at` - Should be null

---

### Test 4: Paid Event Registration (Optional - if payment configured)
**Starting Point:** Any paid event details page
- [ ] Click "Proceed to Payment" button
- [ ] Redirect to payment page
- [ ] Order summary displays
- [ ] Event name, price, qty show
- [ ] Payment method selector visible (Stripe/SSLCommerz)
- [ ] Continue button ready

**Payment Form:**
- [ ] Payment form loads
- [ ] Form fields present
- [ ] Submit button clickable

**Note:** Skip actual payment - close at this stage

---

### Test 5: My Registrations Dashboard
**URL:** `http://localhost:8000/my-registrations` (requires login)
- [ ] Page loads
- [ ] Tab navigation visible: "All", "Upcoming", "Past", "Attended"
- [ ] All registrations display
- [ ] Event cards show: image, title, date, location
- [ ] Status badges show correctly (past/pending/confirmed)
- [ ] Registration code displayed

**Tab Filtering:**
- [ ] Click "Upcoming" → Shows only future events
- [ ] Click "Past" → Shows only past events
- [ ] Click "Attended" → Shows only attended (after check-in test)
- [ ] Count updates per tab

**Action Buttons:**
- [ ] "View Ticket" → Takes to confirmation
- [ ] "Download Certificate" → Shows only for attended (after test 9)
- [ ] "Complete Payment" → Shows for unpaid (if applicable)

---

## Admin Panel Tests

### Test 6: Admin Events List
**URL:** `http://localhost:8000/admin/events` (requires login + admin role)
- [ ] Page loads
- [ ] All events display in table/list
- [ ] Columns: Title, Date, Price, Capacity, Status, Actions
- [ ] Search/Filter works (if implemented)
- [ ] Sort by date/title works
- [ ] Pagination works

**Action Buttons:**
- [ ] Edit button → Takes to edit form
- [ ] View button → Shows event details
- [ ] Delete button (with confirmation)

---

### Test 7: Create Event (Admin)
**URL:** `http://localhost:8000/admin/events/create`
- [ ] Form loads completely
- [ ] All fields present:
  - [ ] Title (required)
  - [ ] Description (required)
  - [ ] Event Type (workshop/exhibition/etc)
  - [ ] Start DateTime (required)
  - [ ] End DateTime (required)
  - [ ] Registration Deadline
  - [ ] Location
  - [ ] Venue Name
  - [ ] Venue Address
  - [ ] City (dropdown)
  - [ ] Event Type (free/paid)
  - [ ] Price (if paid)
  - [ ] Capacity
  - [ ] Banner Image (upload)
  - [ ] Mentors (multiselect if implemented)
  - [ ] Certificates Enabled (checkbox)
  - [ ] Status (draft/published)

**Form Submission:**
- [ ] Fill all required fields
- [ ] Upload test banner image
- [ ] Click Create/Save
- [ ] Validation messages appear for empty fields (test intentionally)
- [ ] Success page shows after valid submission
- [ ] New event visible in events list

---

### Test 8: Edit Event (Admin)
**URL:** `http://localhost:8000/admin/events/{id}/edit`
- [ ] Form pre-fills with current event data
- [ ] All fields editable
- [ ] Can change title
- [ ] Can update description
- [ ] Can change date/time
- [ ] Can modify price
- [ ] Can enable/disable certificates
- [ ] Save button saves changes
- [ ] Changes reflected in event details page

---

### Test 9: Attendance Scanning - Desktop
**URL:** `http://localhost:8000/admin/events/{id}/attendance`
- [ ] Page loads
- [ ] "Attended", "Registered", "Rate" stats display
- [ ] Stats show actual numbers

**Manual Entry (Fallback):**
- [ ] Text input field visible for registration code
- [ ] Placeholder shows expected format (e.g., "REG-XXXXXXXX")
- [ ] Enter the registration code from Test 3 confirmation page
- [ ] Click "Check-in" button
- [ ] Success message appears: "✓ [UserName] checked in"
- [ ] Stats update immediately
- [ ] Recent check-ins list shows the user
- [ ] Timestamp shows current time

**Result in Database:**
- Run in tinker:
- `>>> $reg = \App\Models\EventRegistration::where('registration_code', 'REG-XXXXX')->first();`
- `>>> $reg->attended_at` - Should now show current datetime
- `>>> $reg->attended_at->format('Y-m-d H:i:s')` - Should match on-screen time

---

### Test 10: Attendance Report
**URL:** `http://localhost:8000/admin/events/{id}/attendance/report`
- [ ] Page loads
- [ ] Attended users listed
- [ ] Table shows: Name, Email, Registration Code, Check-in Time
- [ ] Recent attendance visible
- [ ] Export CSV button available (if implemented)

---

### Test 11: Mobile QR Scanner (Advanced - requires HTTPS/mobile)
**URL (on mobile):** `https://localhost:8000/admin/events/{id}/attendance/mobile`
- [ ] Page loads on mobile browser
- [ ] Stats cards display at top
- [ ] Camera permission prompt appears
- [ ] Grant camera permission
- [ ] Video feed shows from device camera
- [ ] "Start Camera" button changes to "Stop Camera"

**QR Scanning:**
- [ ] Print/display QR code from Test 3 confirmation page
- [ ] Point mobile camera at QR code
- [ ] Scan automatically detects QR
- [ ] Success message displays
- [ ] Recent check-ins list updates
- [ ] Stats increment

**Manual Entry:**
- [ ] Stop camera
- [ ] Manual input field becomes active
- [ ] Type registration code
- [ ] Press Enter or click button
- [ ] Check-in succeeds

---

## Certificate Tests

### Test 12: Certificate Auto-Issue Verification
**Prerequisite:** Must complete Test 9 (check-in)

**In Database:**
- Run in tinker:
- `>>> $cert = \App\Models\Certificate::latest()->first();`
- `>>> $cert->certificate_code` - Should show CERT-XXXX-XXXXXXXX format
- `>>> $cert->issued_to_user_id` - Should match the checked-in user
- `>>> $cert->event_id` - Should match the event
- `>>> $cert->status` - Should be "issued"

**Result:** Certificate auto-issued on check-in ✓

---

### Test 13: Certificate Display in User Dashboard
**URL:** `http://localhost:8000/my-registrations`
- [ ] Find the attended event from Test 9/12
- [ ] Status shows "ATTENDED"
- [ ] "Download Certificate" button visible
- [ ] Click button
- [ ] Shows certificate details or download link
- [ ] Certificate code displays
- [ ] Issue date shows current date

---

## API Tests

### Test 14: Public API - Events List
**URL:** `http://localhost:8000/api/v1/events`
- [ ] Returns JSON response
- [ ] HTTP 200 status
- [ ] Response structure:
  ```json
  {
    "success": true,
    "data": [...],
    "pagination": {...}
  }
  ```
- [ ] Events array contains event objects
- [ ] Each event has: id, title, description, price, date, etc.
- [ ] Pagination data present (current_page, per_page, total)

**Test with Query Params:**
- [ ] `?page=2` - Changes page
- [ ] `?per_page=5` - Returns 5 items
- [ ] `?search=workshop` - Filters by search term

### Test 15: Public API - Single Event
**URL:** `http://localhost:8000/api/v1/events/{slug}`
- [ ] Returns JSON
- [ ] HTTP 200 status
- [ ] Event details fully populated
- [ ] Relationships loaded (city, registrations)

### Test 16: Public API - Featured Events
**URL:** `http://localhost:8000/api/v1/events/featured`
- [ ] Returns JSON array of featured events
- [ ] Limited to featured-only events
- [ ] Limit parameter works: `?limit=5`

### Test 17: Public API - Cities
**URL:** `http://localhost:8000/api/v1/events/cities`
- [ ] Returns list of cities
- [ ] Each city shows: id, name, event_count
- [ ] Event counts accurate

---

## Error Handling & Edge Cases

### Test 18: Invalid Inputs
- [ ] Try to register twice for same event → Error message
- [ ] Try invalid registration code → "Not found" message
- [ ] Try non-existent event URL → 404 page
- [ ] Try admin routes as regular user → 403 Forbidden
- [ ] Leave required fields empty → Validation message

### Test 19: Performance
- [ ] Events list loads in < 2 seconds
- [ ] Event details page loads in < 2 seconds
- [ ] Admin pages load in < 3 seconds
- [ ] QR generation completes in < 5 seconds
- [ ] Certificate issuance completes in < 2 seconds

### Test 20: Responsive Design
- [ ] Desktop (1920x1080) → Layout correct
- [ ] Tablet (768px) → Layout adapts
- [ ] Mobile (375px) → Mobile-friendly
- [ ] All buttons clickable on mobile
- [ ] Forms readable on mobile

---

## Summary Report

### Passing Tests: _____ / 20
### Failed Tests: _____ 
### Warnings/Issues: _____

### Critical Issues Found:
(List any breaking bugs here)

### Minor Issues Found:
(List UX improvements or warnings)

### Performance Issues:
(List any slow operations)

---

## Sign-Off Checklist

- [ ] All 20 tests executed
- [ ] No critical issues found
- [ ] Database integrity verified
- [ ] QR generation working
- [ ] Certificate auto-issue working
- [ ] Admin panel functional
- [ ] Public pages functional
- [ ] API endpoints responding
- [ ] Error handling adequate
- [ ] Mobile responsive

**Overall Status:** 🟢 **READY FOR DEPLOYMENT** / 🟡 **NEEDS FIXES** / 🔴 **CRITICAL ISSUES**

**Tester Name:** ___________________
**Date:** February 4, 2026
**Time Spent:** _____ minutes

---

## Notes & Observations

(Add any specific observations, learnings, or recommendations here)

