# QA Testing - Regression Checklist

## 🎯 Pre-Release Testing Checklist

**Release Date**: _____________  
**Tested By**: _____________  
**Date Tested**: _____________  

---

## ✅ PART A: Route & Performance Tests

### Public Routes (GET requests should return 200)
- [ ] `/` - Homepage
- [ ] `/sitemap.xml` - Main sitemap
- [ ] `/sitemap/photographers.xml` - Photographer sitemap
- [ ] `/sitemap/events.xml` - Event sitemap
- [ ] `/sitemap/competitions.xml` - Competition sitemap
- [ ] `/robots.txt` - robots file
- [ ] `/@{username}` - Photographer profile page
- [ ] `/photographer/{id}` - Legacy photographer route

### SEO & API Routes
- [ ] `/api/v1/categories` - Category list
- [ ] `/api/v1/cities` - City list
- [ ] `/api/v1/photographers/search` - Search photographers
- [ ] `/api/photographers/@{username}/portfolio` - Portfolio API
- [ ] `/api/photographers/@{username}/reviews` - Reviews API

### Admin Routes (should redirect if not authenticated)
- [ ] `/admin` - Admin gate
- [ ] `/admin/system-health/sitemap` - Sitemap admin
- [ ] `/admin/dev` - Dev tools (admin only)

---

## ✅ PART B: User Management

### Admin User Creation & Management
- [ ] Admin can create new user (admin role)
- [ ] Admin can create judge user
- [ ] Admin can update user role
- [ ] Admin can delete user
- [ ] Admin can suspend user
- [ ] Suspended user cannot login
- [ ] Email verification required

### User Authentication
- [ ] Regular user can register
- [ ] User can login with credentials
- [ ] Invalid credentials rejected
- [ ] Logout works correctly
- [ ] Session timeout after inactivity

---

## ✅ PART C: Competition Module

### Admin Competition Management
- [ ] Admin can create competition
- [ ] Admin can set competition dates
- [ ] Admin can assign judges to competition
- [ ] Admin can assign sponsors to competition
- [ ] Admin can assign mentors to competition
- [ ] Admin can update competition details
- [ ] Admin can change competition status (draft → published)
- [ ] Admin can delete competition

### Competition Configuration
- [ ] Submission period enforced (cannot submit before/after dates)
- [ ] Voting period enforced
- [ ] Entry fee configured (free or paid)
- [ ] Prize pool configured
- [ ] Max participants limit enforced
- [ ] Judging type selected (scores or votes)
- [ ] Certificate issuance enabled

### Photographer Submissions
- [ ] Photographer can submit to competition
- [ ] Submit button disabled after deadline
- [ ] Max 5 submissions per user enforced
- [ ] Submission awaiting approval initially
- [ ] Can upload image and add description

### Admin Submission Review
- [ ] Admin can view pending submissions
- [ ] Admin can approve submission
- [ ] Admin can reject submission
- [ ] Rejected submissions can be resubmitted

### Voting System
- [ ] Voting only available for approved submissions
- [ ] Client can vote on submission (1-5 rating)
- [ ] One vote per user per submission enforced
- [ ] Cannot vote twice (error or update existing vote)
- [ ] Vote count increments
- [ ] Unapproved submissions receive no votes

### Judge Scoring
- [ ] Admin assigns judges to competition
- [ ] Judge can view assigned competition
- [ ] Judge cannot score unassigned competitions
- [ ] Judge scores submission (1-10 scale)
- [ ] Score criteria optional but recorded if provided
- [ ] Judge cannot score twice (or can update)
- [ ] Average score calculated across judges
- [ ] Ranking determined by average score
- [ ] Top scorer flagged as winner

---

## ✅ PART D: Events Module

### Admin Event Creation
- [ ] Admin can create free event
- [ ] Admin can create paid event
- [ ] Event type selected (workshop, seminar, meetup, conference)
- [ ] Venue details configured (name, address, coordinates)
- [ ] Date/time set correctly
- [ ] Max attendees limit configured
- [ ] Registration deadline enforced
- [ ] Event status set (draft, published, closed)
- [ ] Certificates enabled option available

### Event Updates
- [ ] Admin can edit event details
- [ ] Admin can update venue address
- [ ] Admin can change max attendees
- [ ] Admin can assign mentors
- [ ] Admin can delete event

### Event Registration (Client Side)
- [ ] Client can browse events
- [ ] Event list shows title, date, location
- [ ] Event detail shows full information
- [ ] "Register" button visible before deadline
- [ ] Client can register for free event (no payment)
- [ ] Registration confirmation page shown
- [ ] Client receives confirmation email
- [ ] QR code displayed on confirmation page

### Registration Deadline Enforcement
- [ ] Client cannot register after deadline
- [ ] "Registration Closed" message shown
- [ ] Past events show "Ended"

### Attendee Management (Admin)
- [ ] Admin can view all registrations
- [ ] Admin can scan QR code with mobile device
- [ ] Admin can manually enter registration code
- [ ] Check-in records attendance timestamp
- [ ] Cannot check-in twice (error or silent handling)
- [ ] Attendance report generated

### Client Attendance Feedback
- [ ] After event, client can submit feedback
- [ ] Feedback includes rating (1-5 stars)
- [ ] Feedback includes comment (optional)
- [ ] Feedback saved to database

### Paid Events & Payments
- [ ] Event fee configured
- [ ] Payment gateway selection (SSLCommerz, Stripe)
- [ ] Registration requires payment
- [ ] Payment success/failure handled
- [ ] Registration confirmed only after payment

---

## ✅ PART E: Photographer Verification

### Verification Request Submission
- [ ] Photographer can submit verification request
- [ ] Verification type selected (portfolio, credentials, experience)
- [ ] Documents/links attachable
- [ ] Notes field available
- [ ] Request saved with "pending" status

### Admin Verification Review
- [ ] Admin can view pending verification requests
- [ ] Admin can view request details
- [ ] Admin can approve request
- [ ] Admin can reject with reason
- [ ] Request status updated correctly

### Verification Badge
- [ ] Verified photographers show badge
- [ ] Badge visible on profile
- [ ] Badge appears in directory listings
- [ ] Badge visible to other users

### Verification Resubmission
- [ ] Rejected requests can be resubmitted
- [ ] Duplicate pending requests prevented
- [ ] Approved verification cannot be revoked (in v1)

---

## ✅ PART F: Booking System

### Client Booking Request
- [ ] Client can create booking request
- [ ] Request includes: event type, date, location, hours, budget
- [ ] Special requirements optional
- [ ] Request sent to photographer

### Photographer Response
- [ ] Photographer receives request notification
- [ ] Can accept booking
- [ ] Can reject booking
- [ ] Can propose price/hours different from request
- [ ] Response logged with timestamp

### Client Acceptance
- [ ] Client receives photographer's proposal
- [ ] Can accept proposal
- [ ] Can reject and close request
- [ ] Can counter-propose
- [ ] Status updated to "confirmed"

### Booking Messages
- [ ] Client and photographer can message
- [ ] Messages visible in conversation thread
- [ ] New messages trigger notification
- [ ] Message history preserved

### Booking Status Workflow
- [ ] Pending → Accepted → Confirmed (final status)
- [ ] Pending → Rejected (closed)
- [ ] Cannot change status backwards

---

## ✅ PART G: QR & Attendance (Events)

### QR Generation
- [ ] QR code generated on registration
- [ ] QR unique per registration
- [ ] QR stored on disk: `public/qr-codes/events/{event_id}/{code}.png`
- [ ] QR readable by mobile scanners

### QR Scanning (Mobile)
- [ ] Admin mobile scanner loads
- [ ] Camera permission requested
- [ ] QR successfully scans
- [ ] Manual code entry works
- [ ] Check-in confirmation shown

### Attendance Logging
- [ ] Attendance timestamp recorded
- [ ] Duplicate check-in prevented
- [ ] Attendance report shows all attendees
- [ ] No-show attendees marked

---

## ✅ PART H: Certificates

### Certificate Configuration
- [ ] Admin can create certificate template
- [ ] Template includes: title, description, background image
- [ ] Font selection available
- [ ] Colors customizable
- [ ] Template marked as default

### Certificate Auto-Issuance
- [ ] Certificates auto-issue on event attendance check-in
- [ ] Unique certificate code generated
- [ ] Certificate tracked in database
- [ ] Certificate status: "issued"

### Certificate Viewing
- [ ] Client can view issued certificates
- [ ] Certificate shows event name, date, attendee name
- [ ] Certificate verifiable via unique code
- [ ] PDF download available

---

## ✅ PART I: Database & Performance

### Database Integrity
- [ ] No orphaned records (foreign key constraints)
- [ ] Pivot tables (many-to-many) working correctly
- [ ] Soft deletes not breaking queries
- [ ] Indexes present on frequently queried fields

### Performance Benchmarks
- [ ] Homepage loads < 1 second
- [ ] List pages load < 500ms
- [ ] API endpoints respond < 300ms (average)
- [ ] Search completes < 800ms
- [ ] No N+1 query problems

### Database Queries
- [ ] Eager loading reduces queries
- [ ] Pagination working (10/20 per page)
- [ ] Filtering reduces result set
- [ ] Sorting works on all listable columns

---

## ✅ PART J: Authorization & Security

### Role-Based Access Control
- [ ] Admin-only routes blocked for non-admin
- [ ] Judge routes blocked for non-judge
- [ ] User cannot view other user's data
- [ ] Photographer cannot modify other's profile

### Authorization Checks
- [ ] Only competition judge can score
- [ ] Only booking photographer can accept/reject
- [ ] Only verification admin can approve/reject
- [ ] Only event admin can check in attendees

### Data Protection
- [ ] Passwords hashed (not plain text)
- [ ] Email addresses not exposed in lists
- [ ] API tokens required for authenticated endpoints
- [ ] CSRF tokens present on forms

---

## ✅ PART K: Error Handling

### User-Facing Errors
- [ ] Validation errors shown clearly
- [ ] 404 page displayed for missing resources
- [ ] 403 page displayed for unauthorized access
- [ ] Error messages helpful (not "error")
- [ ] Errors logged but not exposed

### Edge Cases
- [ ] Registration after deadline prevented
- [ ] Voting after competition ends prevented
- [ ] Duplicate bookings prevented
- [ ] Invalid role assignments prevented

---

## ✅ PART L: Email Notifications

### Registration Confirmations
- [ ] Event registration confirmation email sent
- [ ] Competition submission confirmation sent
- [ ] Verification request confirmation sent

### Status Updates
- [ ] Submission approval notification
- [ ] Submission rejection notification
- [ ] Verification approval notification
- [ ] Booking proposal notification
- [ ] Check-in confirmation to client

### Admin Alerts
- [ ] New verification request alert
- [ ] New booking request alert
- [ ] Submission requires review alert

---

## ✅ PART M: Mobile Responsiveness

### Event Pages
- [ ] Event list works on mobile
- [ ] Event detail readable on mobile
- [ ] Registration form responsive
- [ ] QR code displays properly

### Admin Mobile
- [ ] Mobile scanner interface loads
- [ ] Camera accessible
- [ ] Landscape/portrait modes work
- [ ] Touch controls responsive

### Profile Pages
- [ ] Photographer profile responsive
- [ ] Portfolio grid adjusts to screen size
- [ ] Text readable without zoom

---

## ✅ PART N: API & Integration

### REST API Endpoints
- [ ] GET requests return JSON
- [ ] POST requests create records
- [ ] PUT requests update records
- [ ] DELETE requests remove records
- [ ] Error responses include status code + message

### Response Format
- [ ] All responses wrapped in standard format
- [ ] HTTP status codes correct (200, 201, 400, 401, 404, 422)
- [ ] Error messages helpful
- [ ] Pagination metadata included

### Authentication
- [ ] API token required for protected endpoints
- [ ] Invalid token returns 401
- [ ] Expired token returns 401
- [ ] Rate limiting applied (prevents abuse)

---

## 🎯 Test Results Summary

```
Total Tests Run:    _______
Tests Passed:       _______
Tests Failed:       _______
Skipped Tests:      _______
Success Rate:       _______%

Coverage:
  - Lines:          ______%
  - Methods:        ______%
  - Classes:        ______%
```

---

## 🐛 Issues Found

| Issue # | Module | Description | Severity | Status |
|---------|--------|-------------|----------|--------|
| 1 | | | | |
| 2 | | | | |
| 3 | | | | |

---

## ✍️ Sign-Off

**QA Engineer**: ________________________  
**Date**: ________________________  
**Result**: ☐ PASS ☐ FAIL ☐ CONDITIONAL  
**Notes**: ________________________________________________

---

**This checklist covers 8+ major modules and 60+ individual test cases.**
