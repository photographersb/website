# Events Module - Complete Implementation Guide

## ✅ COMPLETED IMPLEMENTATIONS

### 1. Database Migrations (4 tables)
All migrations created and ready to run:
```bash
php artisan migrate
```

**Tables created:**
- `events` - 15 columns with status/type tracking
- `event_tickets` - Multiple ticket types per event
- `event_registrations` - RSVP/booking with QR tokens
- `event_payments` - Payment transaction logging

### 2. Models (4 files)
- **Event.php** - Core event entity with scopes, accessors, and relationships
- **EventTicket.php** - Ticket management with availability tracking
- **EventRegistration.php** - Registration with QR token auto-generation
- **EventPayment.php** - Payment tracking and status management

### 3. Controllers (4 files)

#### EventController.php (Public API)
- `GET /api/v1/events` - List events with filters
- `GET /api/v1/events/{slug}` - Event detail by slug with schema.org SEO
- `GET /api/v1/events/featured` - Featured events
- `POST /api/v1/events/{id}/rsvp` - RSVP for free events
- `GET /api/v1/my-events` - User's registrations

#### EventAdminController.php (Admin CRUD)
- `GET /api/v1/admin/events` - Admin event listing
- `POST /api/v1/admin/events` - Create event
- `PUT /api/v1/admin/events/{id}` - Update event
- `DELETE /api/v1/admin/events/{id}` - Delete event
- `POST /api/v1/admin/events/{id}/toggle-publish` - Publish/unpublish
- `POST /api/v1/admin/events/{id}/toggle-featured` - Feature toggle
- `POST /api/v1/admin/events/{id}/manage-tickets` - Bulk ticket management
- `GET /api/v1/admin/events/{id}/export-attendees` - CSV export
- `GET /api/v1/admin/events/{id}/payment-report` - Payment analytics
- `GET /api/v1/admin/events/{id}/check-in-report` - Attendance analytics

#### EventPaymentController.php (Payment Processing)
- `POST /api/v1/payments/events/initiate` - Create registration + payment record
- `POST /api/v1/payments/events/callback` - Handle payment gateway response
- `GET /api/v1/payments/events/verify` - Check payment status

#### EventCheckInController.php (NEW - Attendance Scanning)
- `GET /api/v1/admin/events/{event}/check-in` - Check-in dashboard
- `POST /api/v1/admin/events/{event}/check-in/scan` - QR scan processing
- `GET /api/v1/admin/events/{event}/check-in/registrations` - Searchable attendee list
- `GET /api/v1/admin/events/{event}/check-in/qr/{token}` - QR token lookup
- `POST /api/v1/admin/events/{event}/check-in/manual` - Manual check-in
- `POST /api/v1/admin/registrations/{registration}/check-in/undo` - Reverse check-in
- `GET /api/v1/admin/events/{event}/check-in/export` - CSV check-in report

### 4. Form Request Validations (NEW - 4 files)
- **StoreEventRequest.php** - Create event validation
- **UpdateEventRequest.php** - Update event validation
- **StoreEventTicketRequest.php** - Ticket creation validation
- **InitiateEventPaymentRequest.php** - Payment initiation validation

### 5. Database Seeder (NEW)
- **EventSeeder.php** - Creates 6 demo events including "bangladesh-wedding-expo" slug with:
  - Free events (no payment)
  - Paid events (multiple ticket types)
  - Featured events
  - Full seed data with realistic dates

### 6. Routes Registration
Added to `/routes/api.php`:
- Public event routes
- Admin event CRUD routes
- **NEW: Event check-in routes** (7 endpoints for attendance management)

---

## 🚀 NEXT STEPS

### Step 1: Run Migrations
```bash
php artisan migrate
```

### Step 2: Seed Demo Data
```bash
php artisan db:seed --class=EventSeeder
```

### Step 3: QR Code Library Installation
```bash
composer require chillerlan/php-qrcode
```

### Step 4: Payment Gateway Configuration
Create `config/payment.php`:
```php
return [
    'sslcommerz' => [
        'store_id' => env('SSLCOMMERZ_STORE_ID'),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
        'api_url' => env('SSLCOMMERZ_API_URL', 'https://sandbox.sslcommerz.com'),
    ],
];
```

### Step 5: Create Vue Components
Required components for frontend:
- **PublicEventDetailPage.vue** - Event display with CTA buttons
- **EventCheckInScanner.vue** - Camera input + QR parsing
- **UserEventsDashboard.vue** - My Events + Registrations

### Step 6: Event Policies (Optional but Recommended)
Create authorization logic in `app/Policies/EventPolicy.php`

### Step 7: SSLCommerz Integration
Implement actual API calls in `EventPaymentController::prepareSSLCommerz()`

---

## 📊 DATA FLOW

### Free Event RSVP Flow
```
User → POST /rsvp → EventController
  ↓
  Create EventRegistration (status=confirmed)
  ↓
  User added to attendee list immediately
```

### Paid Event Registration Flow
```
User → POST /payments/events/initiate → EventPaymentController
  ↓
  Create EventRegistration (status=pending_payment)
  Create EventPayment (status=pending)
  ↓
  Return SSLCommerz redirect URL
  ↓
  User pays → SSLCommerz callback
  ↓
  EventPaymentController::callback()
  ↓
  Mark EventPayment as completed
  Mark EventRegistration as confirmed
```

### Check-in Flow (Event Day)
```
Staff opens check-in scanner
  ↓
Scan QR code (contains qr_token)
  ↓
POST /admin/events/{event}/check-in/scan
  ↓
EventCheckInController::scan()
  ↓
Validate: qr_token exists, belongs to event, status=confirmed, not already attended
  ↓
Mark attendance: status=attended, attended_at=now(), checked_in_by=staff_user_id
  ↓
Return success/failure response
```

---

## 🔍 KEY FEATURES IMPLEMENTED

### 1. Event Management
- ✅ Slug-based routing (unique URL per event)
- ✅ Event types: FREE and PAID
- ✅ Status tracking: draft, published, cancelled
- ✅ Capacity management
- ✅ Booking windows (booking_close_datetime)
- ✅ Featured events highlighting
- ✅ Image/banner support

### 2. Ticket System
- ✅ Multiple ticket types per event
- ✅ Price and quantity per ticket
- ✅ Sales window dates (sales_start_datetime, sales_end_datetime)
- ✅ Active/inactive toggle
- ✅ Availability tracking (quantity - sold_count)

### 3. Registration & Payment
- ✅ QR token auto-generation (32-char random string)
- ✅ Status tracking: pending_payment, confirmed, cancelled, refunded, attended
- ✅ Payment gateway integration skeleton (SSLCommerz)
- ✅ Transaction logging with raw response storage
- ✅ Atomic database transactions for payment processing

### 4. Attendance Scanning
- ✅ QR code generation per registration
- ✅ QR token validation
- ✅ Check-in timestamp tracking (attended_at)
- ✅ Staff tracking (checked_in_by admin_id)
- ✅ Double check-in prevention
- ✅ Manual check-in fallback
- ✅ Undo/reverse check-in functionality
- ✅ CSV export of attendance

### 5. Admin Tools
- ✅ Full CRUD operations
- ✅ Bulk ticket management
- ✅ CSV export of attendees
- ✅ Payment reports
- ✅ Check-in reports
- ✅ Publishing controls
- ✅ Featured toggle

### 6. SEO & Structured Data
- ✅ Schema.org Event structured data generation
- ✅ Meta description for events
- ✅ OpenGraph support ready

---

## 📱 API Endpoints Summary

### Public Endpoints
```
GET    /api/v1/events                       # List all published events
GET    /api/v1/events/featured               # Featured events
GET    /api/v1/events/{slug}                # Event detail by slug
POST   /api/v1/events/{id}/rsvp             # RSVP for free events
GET    /api/v1/my-events                    # User's registrations (auth)
```

### Admin Event Management
```
GET    /api/v1/admin/events                           # Admin list
POST   /api/v1/admin/events                           # Create
PUT    /api/v1/admin/events/{id}                      # Update
DELETE /api/v1/admin/events/{id}                      # Delete
POST   /api/v1/admin/events/{id}/toggle-featured      # Feature
POST   /api/v1/admin/events/{id}/manage-tickets       # Tickets
GET    /api/v1/admin/events/{id}/export-attendees    # CSV
GET    /api/v1/admin/events/{id}/payment-report      # Payments
GET    /api/v1/admin/events/{id}/check-in-report     # Attendance
```

### Payment Processing
```
POST   /api/v1/payments/events/initiate               # Create payment
POST   /api/v1/payments/events/callback               # Handle response
GET    /api/v1/payments/events/verify                 # Verify status
```

### Check-in & Attendance
```
GET    /api/v1/admin/events/{event}/check-in                      # Dashboard
POST   /api/v1/admin/events/{event}/check-in/scan                 # Scan QR
GET    /api/v1/admin/events/{event}/check-in/registrations       # Attendees
GET    /api/v1/admin/events/{event}/check-in/qr/{token}          # Lookup QR
POST   /api/v1/admin/events/{event}/check-in/manual               # Manual
POST   /api/v1/admin/registrations/{registration}/check-in/undo   # Undo
GET    /api/v1/admin/events/{event}/check-in/export              # CSV
```

---

## 🗄️ Database Schema

### events table
- id, title, slug (unique), category_id, organizer_id, description
- location_text, city_id, venue, latitude, longitude
- start_datetime, end_datetime
- event_type (free/paid), base_price (nullable), capacity (nullable)
- booking_close_datetime
- refund_policy (text)
- banner_image (path)
- status (draft/published/cancelled)
- is_featured (boolean)
- created_by, created_at, updated_at

### event_tickets table
- id, event_id (FK), title, price, quantity, sold_count
- sales_start_datetime, sales_end_datetime
- is_active (boolean)
- created_at, updated_at

### event_registrations table
- id, event_id (FK), user_id (FK), ticket_id (FK, nullable)
- quantity, total_amount
- status (pending_payment/confirmed/cancelled/refunded/attended)
- qr_token (unique, 32-char random string)
- attended_at (timestamp, nullable)
- checked_in_by (FK to User, nullable - admin who marked attendance)
- created_at, updated_at

### event_payments table
- id, event_registration_id (FK), gateway
- transaction_id (unique), amount, currency
- status (pending/completed/failed/cancelled)
- paid_at (timestamp)
- raw_response (JSON)
- created_at, updated_at

---

## ⚡ QUICK TEST

```bash
# 1. Run migrations
php artisan migrate

# 2. Seed demo data
php artisan db:seed --class=EventSeeder

# 3. Test public listing
curl http://localhost/api/v1/events

# 4. Test event detail by slug
curl http://localhost/api/v1/events/bangladesh-wedding-expo

# 5. Test featured events
curl http://localhost/api/v1/events/featured
```

---

## 📝 Configuration Files Needed

1. **config/payment.php** - Payment gateway configuration
2. **.env updates**:
   ```
   SSLCOMMERZ_STORE_ID=your_store_id
   SSLCOMMERZ_STORE_PASSWORD=your_password
   SSLCOMMERZ_API_URL=https://sandbox.sslcommerz.com
   ```

---

## 🎯 Validation Rules

### Create/Update Event
- title: required, max 255
- description: required
- category_id: required, exists
- city_id: required, exists
- venue: required, max 255
- start_datetime: required, date, after now
- end_datetime: required, date, after start_datetime
- event_type: required, in:free,paid
- base_price: required_if paid, numeric, min 0
- booking_close_datetime: required, date, before start_datetime

### RSVP (Free Events)
- Validation: event exists, is_free, capacity available, booking window open
- Action: Create registration with status=confirmed

### Initiate Payment (Paid Events)
- Validation: event exists, is_paid, ticket exists, quantity available
- Action: Create registration (pending_payment) + payment (pending) + return gateway URL

### Scan QR (Check-in)
- Validation: qr_token exists, belongs to event, status=confirmed, not already attended
- Action: Mark attended with timestamp and staff user

---

## ✨ BEST PRACTICES IMPLEMENTED

✅ Eager loading with relationships (prevent N+1)
✅ Database transactions for payment atomicity
✅ Scopes for common queries
✅ Accessors for computed properties
✅ Unique constraints (slug, qr_token, transaction_id)
✅ Strategic indexing on frequently queried columns
✅ Proper cascade delete behavior
✅ Enum types for status fields
✅ ISO 8601 datetime formatting
✅ Comprehensive error handling

---

## 📞 Support

All models, controllers, and migrations are fully documented with PHP docblocks.
All validation rules are specified with clear error messages.
All relationships are properly defined with eager loading patterns.

