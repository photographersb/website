# BOOKING MARKETPLACE - DEPLOYMENT CHECKLIST

## Pre-Deployment Verification

### ✅ Code Quality
- [x] All PHP files pass syntax validation
  - [x] 3 Controllers (280+ lines combined)
  - [x] 3 Models (complete with relationships)
  - [x] 1 Policy (complete authorization)
  - [x] 5 Notifications (async configured)
  - [x] 3 Form Requests (validation rules)
- [x] All Vue components created (5 pages)
- [x] All routes registered (15 routes)
- [x] All database migrations ready (3 tables)

### ✅ Database
- [x] `booking_requests` table with 25 fields
- [x] `booking_messages` table with 7 fields
- [x] `booking_status_logs` table with 6 fields
- [x] All foreign key relationships defined
- [x] All indexes created (for performance)
- [x] Enum types properly configured

### ✅ Architecture
- [x] Policy-based authorization implemented
- [x] Form request validation in place
- [x] Eager-loading patterns documented
- [x] Async notification queuing enabled
- [x] Status audit trail immutable

---

## Deployment Steps

### Step 1: Database Migration
```bash
# Run migrations
php artisan migrate

# Verify tables created
php artisan tinker
>>> DB::table('booking_requests')->count()  # Should return 0
>>> DB::table('booking_messages')->count()  # Should return 0
>>> DB::table('booking_status_logs')->count()  # Should return 0
```

### Step 2: Cache & Config
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Rebuild configs
php artisan config:cache
php artisan route:cache
```

### Step 3: Frontend Assets
```bash
# Build Vue components
npm run build

# Or in development
npm run dev
```

### Step 4: Verify Routes
```bash
# List all booking routes
php artisan route:list | grep booking

# Should show 15 routes:
#  booking.create
#  booking.store
#  booking.show
#  booking.client.list
#  booking.photographer.list
#  booking.accept
#  booking.decline
#  booking.cancel
#  booking.complete
#  booking.message.store
#  booking.message.delete
#  booking.messages.read
#  admin.booking.index
#  admin.booking.show
#  admin.booking.cancel
#  admin.booking.dispute
```

### Step 5: Test Core Functionality

#### 5a. Test Booking Creation
```php
php artisan tinker

# Create test users
$client = User::factory()->create(['is_photographer' => false]);
$photographer = User::factory()->create(['is_photographer' => true]);

# Create test booking
$booking = BookingRequest::create([
    'client_user_id' => $client->id,
    'photographer_user_id' => $photographer->id,
    'booking_code' => 'SB-BK-2026-0001',
    'event_date' => now()->addDays(10),
    'status' => 'pending',
    'budget_min' => 5000,
    'budget_max' => 10000,
]);

# Should succeed and return BookingRequest instance
dump($booking);
```

#### 5b. Test Authorization Policy
```php
# Test policy rules
auth()->setUser($client);
$client->can('create', BookingRequest::class);  # true
$client->can('accept', $booking);               # false (only photographer)

auth()->setUser($photographer);
$photographer->can('accept', $booking);         # true
$photographer->can('view', $booking);           # true
```

#### 5c. Test State Transitions
```php
# Test booking state changes
$booking->update(['status' => 'accepted', 'accepted_at' => now()]);
$booking->isAccepted();  # true

# Log state change
$booking->statusLogs()->create([
    'old_status' => 'pending',
    'new_status' => 'accepted',
    'changed_by_user_id' => $photographer->id,
    'note' => 'Booking accepted by photographer',
]);

# Verify log created
$booking->statusLogs()->count();  # 1
```

#### 5d. Test Messaging
```php
# Create message
$message = $booking->messages()->create([
    'sender_user_id' => $client->id,
    'message' => 'Can you include drone photography?',
]);

# Mark as read
$message->markAsRead();
$message->is_read;  # true

# Test message retrieval
$booking->getUnreadMessageCount();  # 0
```

#### 5e. Test Notifications
```php
# Dispatch notification (should queue)
$photographer->notify(new BookingRequestCreated($booking));

# Check database notifications table
DB::table('notifications')->latest()->first();

# Check queue (should be processing)
php artisan queue:work
```

### Step 6: API Integration Testing

#### Test Client Routes
```bash
# Login as client
curl -X POST http://localhost/login \
  -H "Content-Type: application/json" \
  -d '{"email":"client@example.com","password":"password"}'

# Get booking form
curl http://localhost/@photographer_username/book

# Create booking
curl -X POST http://localhost/bookings \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "photographer_user_id": 2,
    "event_date": "2026-03-15",
    "budget_min": 5000,
    "budget_max": 10000
  }'
```

#### Test Photographer Routes
```bash
# Login as photographer
# List bookings
curl http://localhost/my-bookings/photographer \
  -H "Authorization: Bearer TOKEN"

# Accept booking
curl -X POST http://localhost/bookings/1/accept \
  -H "Authorization: Bearer TOKEN"
```

#### Test Admin Routes
```bash
# Login as super admin
# List all bookings
curl http://localhost/admin/bookings \
  -H "Authorization: Bearer TOKEN"

# Get statistics
curl http://localhost/admin/bookings/statistics/get \
  -H "Authorization: Bearer TOKEN"
```

### Step 7: UI Component Verification

#### Verify Vue Components Loaded
```bash
# Check browser console after navigating to:
# /@username/book              → Create.vue
# /bookings/1                  → Show.vue
# /my-bookings/client          → ClientList.vue
# /my-bookings/photographer    → PhotographerList.vue
# /admin/bookings/1            → Admin/Show.vue
```

#### Test Form Submission
- Navigate to `/@photographer/book`
- Fill out all fields
- Submit form
- Should redirect to booking detail page

---

## Post-Deployment Verification

### ✅ Database Health
```bash
# Check table row counts
php artisan tinker
>>> DB::table('booking_requests')->count()
>>> DB::table('booking_messages')->count()
>>> DB::table('booking_status_logs')->count()

# Check indexes
>>> DB::table('booking_requests')->where('status', 'accepted')->get();
# Should use index, verify with EXPLAIN
```

### ✅ Notifications Delivery
```bash
# Monitor queue
php artisan queue:monitor

# Check notification logs
>>> DB::table('notifications')->count()

# Verify email deliverability
# Check email logs or SMTP driver
```

### ✅ Performance Monitoring
```bash
# Check slow queries
php artisan tinker
>>> BookingRequest::with('client', 'photographer', 'messages.sender')->paginate(10);
# Should not generate N+1 queries

# Monitor query count with debugging
>>> DB::enableQueryLog();
>>> BookingRequest::with('client', 'photographer', 'messages.sender')->get();
>>> count(DB::getQueryLog());  # Should be ~3-5, not 100+
```

### ✅ Security Verification
```bash
# Test authorization (should fail)
php artisan tinker
>>> $booking = BookingRequest::find(1);
>>> auth()->setUser($randomUser);
>>> auth()->user()->can('view', $booking);  # false

# Test form validation
# Submit booking without required fields
# Should see validation errors

# Test file upload restrictions
# Try uploading executable file
# Should be rejected (mime type validation)
```

---

## Monitoring & Maintenance

### Daily Checks
- [ ] Monitor error logs for booking-related errors
- [ ] Check notification delivery status
- [ ] Verify no database connection issues
- [ ] Monitor queue processing

### Weekly Tasks
- [ ] Review booking statistics
- [ ] Check for disputed bookings
- [ ] Monitor performance metrics
- [ ] Review user feedback

### Monthly Tasks
- [ ] Analyze booking patterns
- [ ] Optimize slow queries if any
- [ ] Review notification templates
- [ ] Audit admin actions
- [ ] Backup database

---

## Rollback Plan

If issues occur post-deployment:

### Quick Rollback
```bash
# Rollback migrations (if needed)
php artisan migrate:rollback

# Clear caches
php artisan cache:clear
php artisan config:clear

# Restore previous code version from git
git checkout HEAD~1

# Rebuild assets
npm run build
```

### Partial Rollback
If only certain routes have issues:
- Remove problematic routes from routes/web.php
- Clear route cache: `php artisan route:clear`
- Users will see 404 for those routes
- Fix issue and re-enable

### Database Recovery
If data is corrupted:
- Don't delete tables directly
- Use migrations to alter structure
- Keep backups before any migration
- Test migrations on staging first

---

## Common Issues & Solutions

### Issue: Routes not found (404)
**Solution:**
```bash
php artisan route:clear
php artisan route:cache
```

### Issue: Policy authorization not working
**Solution:**
```bash
php artisan config:clear
# Verify User model has authorization methods
# Check policy is registered in AuthServiceProvider
```

### Issue: Notifications not sending
**Solution:**
```bash
# Check queue is running
php artisan queue:work

# Check notification table has entries
DB::table('notifications')->count()

# Verify MAIL_* config in .env
```

### Issue: File uploads failing
**Solution:**
```bash
# Check storage/app/public/booking-attachments permissions
chmod -R 755 storage/app/public/booking-attachments

# Verify storage link exists
php artisan storage:link

# Check upload_max_filesize in php.ini (should be > 10MB)
```

### Issue: Database migration failed
**Solution:**
```bash
# Check foreign key constraints
# Verify all referenced tables exist

# Reset and retry
php artisan migrate:rollback
php artisan migrate

# Or fix specific migration
# Edit migration file, then:
php artisan migrate --path=database/migrations/2026_02_04_*.php
```

---

## Success Criteria

✅ Deployment is successful if:

1. [ ] All 3 migrations run without errors
2. [ ] All 15 routes registered and accessible
3. [ ] Booking creation works end-to-end
4. [ ] Authorization policy blocks unauthorized access
5. [ ] Notifications queue and deliver
6. [ ] Messages can be sent and retrieved
7. [ ] Admin can view and filter bookings
8. [ ] Status transitions work correctly
9. [ ] Audit logs record all changes
10. [ ] No N+1 query issues
11. [ ] Performance acceptable (< 200ms response time)
12. [ ] UI components render correctly
13. [ ] Form validation works
14. [ ] File uploads work and validate
15. [ ] All error handling works gracefully

---

## Support & Documentation

- **Complete Implementation Guide:** `BOOKING_MARKETPLACE_COMPLETE.md`
- **Quick Reference:** `BOOKING_MARKETPLACE_QUICK_REF.md`
- **Code Files:**
  - Models: `app/Models/Booking*.php`
  - Controllers: `app/Http/Controllers/BookingRequestController.php`
  - Admin Controller: `app/Http/Controllers/Admin/BookingController.php`
  - Policy: `app/Policies/BookingRequestPolicy.php`
  - Notifications: `app/Notifications/Booking*.php`
  - Form Requests: `app/Http/Requests/*BookingRequest.php`
  - Vue Components: `resources/js/Pages/Bookings/*.vue`
  - Admin Views: `resources/js/Pages/Admin/Bookings/*.vue`

---

## Final Checklist Before Go-Live

- [ ] All code reviewed and approved
- [ ] Database migrations tested on staging
- [ ] API endpoints tested with real data
- [ ] UI components tested in all browsers
- [ ] Notifications tested with real email
- [ ] Performance benchmarked (< 200ms)
- [ ] Security review completed
- [ ] Error handling verified
- [ ] Rollback plan documented
- [ ] Team trained on new feature
- [ ] User documentation prepared
- [ ] Support team briefed
- [ ] Monitoring configured
- [ ] Backup system verified
- [ ] Go-live checklist signed off

---

**Status:** Ready for Production Deployment  
**Last Updated:** 2026-02-05  
**System:** Photographer SB v1.0 - Booking Marketplace
