# Events Module - Quick Reference Card

## 🚀 LAUNCH IN 3 STEPS

```bash
# Step 1: Run migrations
php artisan migrate

# Step 2: Seed demo data
php artisan db:seed --class=EventSeeder

# Step 3: Test API
curl http://localhost/api/v1/events
```

---

## 📊 DATABASE TABLES

### events (15 columns)
```sql
id, title, slug (unique), category_id, organizer_id, description
location_text, city_id, venue, latitude, longitude
start_datetime, end_datetime, event_type, base_price, capacity
booking_close_datetime, refund_policy, banner_image
status, is_featured, created_by, created_at, updated_at
```

### event_tickets (8 columns)
```sql
id, event_id, title, price, quantity, sold_count
sales_start_datetime, sales_end_datetime, is_active, created_at, updated_at
```

### event_registrations (11 columns)
```sql
id, event_id, user_id, ticket_id, quantity, total_amount
status, qr_token (unique), attended_at, checked_in_by, created_at, updated_at
```

### event_payments (9 columns)
```sql
id, event_registration_id, gateway, transaction_id (unique)
amount, currency, status, paid_at, raw_response (JSON), created_at, updated_at
```

---

## 🔗 API ENDPOINTS (Quick Reference)

### Public API
```
GET    /api/v1/events                    # List all published events
GET    /api/v1/events/featured           # Featured events
GET    /api/v1/events/{slug}             # Event details
POST   /api/v1/events/{id}/rsvp          # RSVP (free events)
GET    /api/v1/my-events                 # My registrations (auth)
```

### Admin Management
```
GET    /api/v1/admin/events              # List (admin)
POST   /api/v1/admin/events              # Create
PUT    /api/v1/admin/events/{id}         # Update
DELETE /api/v1/admin/events/{id}         # Delete
POST   /api/v1/admin/events/{id}/toggle-featured   # Feature
POST   /api/v1/admin/events/{id}/manage-tickets    # Manage tickets
GET    /api/v1/admin/events/{id}/export-attendees # CSV
GET    /api/v1/admin/events/{id}/payment-report   # Payments
GET    /api/v1/admin/events/{id}/check-in-report  # Attendance
```

### Payments
```
POST   /api/v1/payments/events/initiate  # Start payment
POST   /api/v1/payments/events/callback  # Handle response
GET    /api/v1/payments/events/verify    # Verify status
```

### Check-in Scanning
```
GET    /api/v1/admin/events/{event}/check-in                  # Dashboard
POST   /api/v1/admin/events/{event}/check-in/scan             # Scan QR
GET    /api/v1/admin/events/{event}/check-in/registrations   # Attendees
POST   /api/v1/admin/events/{event}/check-in/manual           # Manual
POST   /api/v1/admin/registrations/{registration}/check-in/undo # Undo
GET    /api/v1/admin/events/{event}/check-in/export           # CSV
```

---

## 📁 KEY FILES

**Models:**
- `app/Models/Event.php`
- `app/Models/EventTicket.php`
- `app/Models/EventRegistration.php`
- `app/Models/EventPayment.php`

**Controllers:**
- `app/Http/Controllers/Api/EventController.php` (public)
- `app/Http/Controllers/Api/EventAdminController.php` (admin)
- `app/Http/Controllers/Api/EventPaymentController.php` (payments)
- `app/Http/Controllers/Api/Admin/EventCheckInController.php` (check-in)

**Migrations:**
- `database/migrations/2026_02_01_000001_create_events_table.php`
- `database/migrations/2026_02_01_000002_create_event_tickets_table.php`
- `database/migrations/2026_02_01_000003_create_event_registrations_table.php`
- `database/migrations/2026_02_01_000004_create_event_payments_table.php`

**Seeder:**
- `database/seeders/EventSeeder.php`

---

## ✅ VALIDATION RULES

### Event Creation
```php
title: required, max:255
description: required
category_id: required, exists:categories,id
city_id: required, exists:cities,id
venue: required, max:255
start_datetime: required, date, after:now
end_datetime: required, date, after:start_datetime
event_type: required, in:free,paid
base_price: required_if:event_type,paid
booking_close_datetime: required, date, before:start_datetime
capacity: nullable, integer, min:1
banner_image: nullable, image, max:5120
is_featured: boolean
```

### RSVP/Registration
```php
Validations:
- Event exists and is published
- Event is free (for RSVP)
- Capacity available
- Booking window open (before booking_close_datetime)
- User not already registered
```

### Payment Initiation
```php
ticket_id: required, exists:event_tickets,id
qty: required, integer, min:1

Validations:
- Ticket belongs to event
- Quantity available
- User not already registered
- Event is paid
- User authenticated
```

---

## 🔄 DATA FLOW

### Free Event
```
User RSVP → EventRegistration (status=confirmed) → Listed in attendees
```

### Paid Event
```
User pays → EventPayment (pending) → SSLCommerz → Callback →
EventPayment (completed) → EventRegistration (confirmed) → Ticket issued
```

### Check-in
```
Scan QR → Validate → Mark attended → Update attendance_at + checked_in_by
```

---

## 🎯 STATUS VALUES

### EventRegistration::status
- `pending_payment` - Waiting for payment
- `confirmed` - Ready to attend
- `attended` - Checked in at event
- `cancelled` - User cancelled
- `refunded` - Payment refunded

### EventPayment::status
- `pending` - Awaiting payment
- `completed` - Successfully paid
- `failed` - Payment failed
- `cancelled` - Payment cancelled

### Event::status
- `draft` - Not published
- `published` - Visible publicly
- `cancelled` - No longer happening

---

## 🔍 USEFUL QUERIES

**List all upcoming published events:**
```php
Event::published()->upcoming()->get();
```

**Get event by slug:**
```php
Event::where('slug', 'bangladesh-wedding-expo')->first();
```

**Get user's registrations:**
```php
auth()->user()->eventRegistrations()->with('event', 'ticket')->get();
```

**Check attendee count:**
```php
$event->registrations()->where('status', 'attended')->count();
```

**Check available tickets:**
```php
$ticket->getAvailableQuantity();
```

**Get completed payments:**
```php
EventPayment::completed()->get();
```

---

## 🧪 TESTING COMMANDS

```bash
# Run migrations
php artisan migrate

# Seed demo data
php artisan db:seed --class=EventSeeder

# Fresh database (careful!)
php artisan migrate:fresh --seed

# Test with artisan tinker
php artisan tinker
>>> Event::all()
>>> Event::published()->get()
>>> EventSeeder::class
```

---

## 📚 DOCUMENTATION FILES

1. **EVENTS_MODULE_IMPLEMENTATION.md** - Full feature overview
2. **SSLCOMMERZ_INTEGRATION_GUIDE.md** - Payment setup
3. **QR_CODE_IMPLEMENTATION_GUIDE.md** - QR code implementation
4. **EVENTS_MODULE_COMPLETE.md** - Complete summary
5. **EVENTS_VERIFICATION_CHECKLIST.md** - Verification steps

---

## 🚨 COMMON ISSUES & FIXES

**Migrations not running:**
```bash
# Check migration files exist
ls database/migrations | grep 2026_02_01

# Clear cache and retry
php artisan cache:clear
php artisan migrate:fresh
```

**Routes not working:**
```bash
# Check routes are registered
php artisan route:list | grep event

# Clear cache
php artisan route:clear
```

**QR code not generating:**
```bash
# Install library
composer require chillerlan/php-qrcode

# Check storage permissions
chmod -R 755 storage/
```

**Payment not working:**
```bash
# Check config
php artisan config:cache

# Verify .env has payment settings
# SSLCOMMERZ_STORE_ID=...
# SSLCOMMERZ_STORE_PASSWORD=...
```

---

## 🔐 AUTHORIZATION

### Who can do what?

| Action | User | Admin | Organizer | Notes |
|--------|------|-------|-----------|-------|
| View published events | ✅ | ✅ | ✅ | Public endpoint |
| RSVP free event | ✅ | ✅ | ✅ | Must be authenticated |
| Initiate payment | ✅ | ✅ | ✅ | Authenticated user |
| Create event | ✅ | ✅ | ✅ | Photographer/Admin |
| Edit own event | ✅ | ✅ | ✅ | Creator or admin |
| Delete event | ❌ | ✅ | ✅ | Admin or creator |
| Export attendees | ❌ | ✅ | ✅ | Admin or organizer |
| Check-in | ❌ | ✅ | ❌ | Admin/staff only |
| View payments | ❌ | ✅ | ✅ | Admin or organizer |

---

## 💾 BACKUP & RESTORE

```bash
# Backup event data
mysqldump -u user -p database events > events_backup.sql
mysqldump -u user -p database event_tickets >> events_backup.sql

# Restore
mysql -u user -p database < events_backup.sql
```

---

## 📱 FRONTEND REQUIREMENTS (Optional)

Vue components needed for full UI:
- PublicEventDetailPage - Event viewing with CTA
- EventListingPage - Browse and filter
- UserEventsDashboard - My registrations
- EventCheckInScanner - QR scanning
- AdminEventManager - CRUD interface
- PaymentCheckout - Payment form

---

## 🎓 LEARNING RESOURCES

**Laravel Concepts Used:**
- Eloquent Models & Relationships
- Query Scopes & Accessors
- Form Requests & Validation
- Controllers & Routing
- Database Migrations
- Model Factories & Seeders
- Authentication & Authorization
- Transactions & Atomicity

---

## 🚀 PERFORMANCE TIPS

1. **Eager Load Relationships**
   ```php
   Event::with(['tickets', 'registrations', 'organizer'])->get();
   ```

2. **Use Pagination**
   ```php
   Event::paginate(15);
   ```

3. **Cache Frequent Queries**
   ```php
   Cache::remember('events-featured', 3600, function() {
       return Event::featured()->get();
   });
   ```

4. **Use Scopes**
   ```php
   Event::published()->featured()->upcoming()->get();
   ```

---

## ✨ YOU'RE READY!

Everything is set up. Just run the 3 launch steps at the top and you're good to go!

For more details, check the documentation files.

Happy building! 🎉

