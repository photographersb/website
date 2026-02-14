# Email Notification System Documentation

## Overview
The Photographar platform features a comprehensive notification system that keeps users informed about bookings, payments, and status changes through both email and in-app notifications.

## Architecture

### Notification Flow
```
Event Trigger → Notification Class → Queue System → Email + Database → User Inbox
```

### Notification Types

1. **BookingCreated** - Sent when new booking is created
   - Client receives: Booking confirmation
   - Photographer receives: New booking alert

2. **BookingStatusUpdated** - Sent when booking status changes
   - Client receives: Status update notification
   - Triggers: confirmed, rejected, completed, cancelled

3. **PaymentReceived** - Sent when payment is completed
   - Client receives: Payment receipt
   - Photographer receives: Payment notification

4. **ReviewRequest** - Sent after service completion
   - Client receives: Request to write review
   - Delayed by 1 day after completion

## File Structure

```
app/
├── Notifications/
│   ├── BookingCreated.php
│   ├── BookingStatusUpdated.php
│   ├── PaymentReceived.php
│   └── ReviewRequest.php
├── Http/Controllers/Api/
│   ├── BookingController.php (sends notifications)
│   ├── NotificationController.php (manages inbox)
│   └── PaymentService.php (sends payment notifications)

resources/js/components/
└── NotificationsInbox.vue (frontend inbox)

database/migrations/
└── xxxx_create_notifications_table.php
```

## Notification Classes

### 1. BookingCreated

**Trigger:** When client creates a booking

**Code Location:** `app/Http/Controllers/Api/BookingController.php`
```php
$booking->client->notify(new BookingCreated($booking, 'client'));
$booking->photographer->user->notify(new BookingCreated($booking, 'photographer'));
```

**Email Content (Client):**
```
Subject: Booking Request Submitted - Photographar
Greeting: Hello [Client Name]!

Your booking request has been successfully submitted.
- Photographer: [Name]
- Event Date: [Date]
- Location: [Location]
- Package: [Package Name]
- Price: ৳[Amount]

The photographer will review your request and respond shortly.

[View Booking Button]

Thank you for choosing Photographar - Across Somogro Bangladesh!
```

**Email Content (Photographer):**
```
Subject: New Booking Request - Photographar
Greeting: Hello [Photographer Name]!

You have received a new booking request.
- Client: [Name]
- Event Date: [Date]
- Location: [Location]
- Package: [Package Name]
- Message: [Client Message]

[View & Respond Button]

Please review and respond to this booking request.
```

### 2. BookingStatusUpdated

**Trigger:** When photographer changes booking status

**Code Location:** `app/Http/Controllers/Api/BookingController.php`
```php
$booking->client->notify(new BookingStatusUpdated($booking, $oldStatus, $newStatus));
```

**Status-Specific Messages:**

**Confirmed:**
```
Subject: Booking Confirmed - Photographar
Greeting: Great News!

Your booking has been confirmed by the photographer.
Next Step: Please complete the payment to finalize your booking.

[Pay Now Button]
```

**Rejected:**
```
Subject: Booking Declined - Photographar
Greeting: Booking Update

Unfortunately, the photographer is not available for your requested date.

[Browse Other Photographers Button]
```

**Completed:**
```
Subject: Service Completed - Photographar
Greeting: Service Complete!

The photographer has marked your booking as completed. 
We hope you had a great experience!

[Write a Review Button]
```

**Cancelled:**
```
Subject: Booking Cancelled - Photographar
Greeting: Booking Cancelled

Your booking has been cancelled.

[View Bookings Button]
```

### 3. PaymentReceived

**Trigger:** When payment callback confirms successful payment

**Code Location:** `app/Services/PaymentService.php`
```php
$transaction->booking->client->notify(new PaymentReceived($transaction, 'client'));
$transaction->booking->photographer->user->notify(new PaymentReceived($transaction, 'photographer'));
```

**Email Content (Client):**
```
Subject: Payment Confirmation - Photographar
Greeting: Hello [Client Name]!

Your payment has been received successfully!

Transaction Details:
- Transaction ID: [TXN123456]
- Amount Paid: ৳15,750
- Payment Method: bKash
- Date: January 27, 2026 10:30 AM

---
Booking Details:
- Photographer: [Name]
- Event Date: [Date]
- Location: [Location]
- Package: [Package Name]
---

Remaining Balance: ৳35,000
The remaining amount will be paid after the service is completed.

[View Receipt Button]

Thank you for your payment!
```

**Email Content (Photographer):**
```
Subject: Payment Received for Booking - Photographar
Greeting: Hello [Photographer Name]!

You have received a payment for a booking.

Transaction Details:
- Transaction ID: [TXN123456]
- Amount: ৳15,750
- Payment Method: bKash

Booking Details:
- Client: [Name]
- Event Date: [Date]
- Location: [Location]
- Package: [Package Name]

[View Booking Button]

Your payout will be processed according to our payment schedule.
```

### 4. ReviewRequest

**Trigger:** 1 day after booking is marked as completed

**Code Location:** `app/Http/Controllers/Api/BookingController.php`
```php
$booking->client->notify((new ReviewRequest($booking))->delay(now()->addDay()));
```

**Email Content:**
```
Subject: Share Your Experience - Photographar
Greeting: Hello [Client Name]!

Thank you for choosing Photographar! We hope you had a wonderful experience.

We would love to hear about your experience with [Photographer Name].

Your feedback helps other clients make informed decisions and helps 
photographers improve their services.

Event Date: [Date]
Location: [Location]

[Write a Review Button]

Your review will be publicly visible on the photographer's profile.

Thank you for supporting our photography community!
```

## In-App Notifications

### Notifications Inbox Component

**Route:** `/notifications`

**Features:**
- ✅ Real-time notification list
- ✅ Unread count badge
- ✅ Filter by all/unread/read
- ✅ Mark as read on click
- ✅ Mark all as read button
- ✅ Auto-navigation to relevant page
- ✅ Icon and color coding by type
- ✅ Relative timestamps ("2 hours ago")

**UI Elements:**
```
+------------------------------------------+
| Notifications         [Mark all as read] |
| Stay updated with your bookings          |
|                                          |
| [All] [Unread (3)] [Read]               |
+------------------------------------------+
| 🔵 New Booking Request                   |
|    Booking for Feb 15, 2026 at Dhaka   |
|    2 hours ago              [View >]    |
+------------------------------------------+
| 💳 Payment Received                      |
|    Payment of ৳15,750 received          |
|    5 hours ago              [View >]    |
+------------------------------------------+
```

### Database Storage

**Table:** `notifications`

**Columns:**
```sql
- id (uuid)
- type (varchar) - Notification class name
- notifiable_type (varchar) - User model
- notifiable_id (bigint) - User ID
- data (json) - Notification details
- read_at (timestamp) - Null for unread
- created_at (timestamp)
- updated_at (timestamp)
```

**Sample Data:**
```json
{
  "booking_id": 1,
  "event_date": "2026-02-15",
  "location": "Dhaka",
  "recipient_role": "client"
}
```

## API Endpoints

### Get User Notifications
**GET** `/api/v1/notifications`

**Query Parameters:**
- `per_page` - Items per page (default: 20)

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "type": "App\\Notifications\\BookingCreated",
      "data": {
        "booking_id": 1,
        "event_date": "2026-02-15",
        "location": "Dhaka"
      },
      "read_at": null,
      "created_at": "2026-01-27T10:30:00"
    }
  ],
  "meta": {
    "total": 25,
    "unread_count": 3
  }
}
```

### Get Unread Count
**GET** `/api/v1/notifications/unread-count`

**Response:**
```json
{
  "status": "success",
  "data": {
    "count": 3
  }
}
```

### Mark as Read
**POST** `/api/v1/notifications/{id}/read`

**Response:**
```json
{
  "status": "success",
  "message": "Notification marked as read"
}
```

### Mark All as Read
**POST** `/api/v1/notifications/mark-all-read`

**Response:**
```json
{
  "status": "success",
  "message": "All notifications marked as read"
}
```

### Delete Notification
**DELETE** `/api/v1/notifications/{id}`

**Response:**
```json
{
  "status": "success",
  "message": "Notification deleted"
}
```

## Configuration

### Mail Settings (.env)

**For Development (Log to File):**
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@photographar.com"
MAIL_FROM_NAME="Photographar Bangladesh"
```

**For Production (SMTP):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@photographar.com"
MAIL_FROM_NAME="Photographar Bangladesh"
```

**For Production (SendGrid):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@photographar.com"
MAIL_FROM_NAME="Photographar Bangladesh"
```

### Queue Configuration

For production, use queue workers to send emails asynchronously:

```env
QUEUE_CONNECTION=database
```

**Run queue worker:**
```bash
php artisan queue:work
```

**Or use supervisor for production:**
```ini
[program:photographar-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

## Testing

### Manual Testing

1. **Test Booking Notification:**
```bash
# Create a booking via UI
# Check storage/logs/laravel.log for email content
```

2. **Test Payment Notification:**
```bash
# Complete a payment
# Check logs for both client and photographer emails
```

3. **Test Status Update:**
```bash
# Change booking status in photographer dashboard
# Check logs for status update email
```

4. **Test Review Request:**
```bash
# Mark booking as completed
# Wait 1 day or manually trigger:
php artisan tinker
>>> $booking = App\Models\Booking::find(1);
>>> $booking->client->notify(new App\Notifications\ReviewRequest($booking));
```

### View Email Content

**Check Logs:**
```bash
tail -f storage/logs/laravel.log | grep -A 50 "Subject:"
```

**Use MailHog (Development):**
```bash
# Install MailHog
# Update .env:
MAIL_HOST=localhost
MAIL_PORT=1025

# Run MailHog:
mailhog

# Visit http://localhost:8025
```

### Database Inspection

**Check notifications table:**
```sql
SELECT id, type, notifiable_id, read_at, created_at 
FROM notifications 
ORDER BY created_at DESC 
LIMIT 10;
```

**Check unread count:**
```sql
SELECT COUNT(*) as unread 
FROM notifications 
WHERE notifiable_id = 2 
AND read_at IS NULL;
```

## Frontend Integration

### Display Unread Count in Header

```vue
<template>
  <router-link to="/notifications" class="relative">
    <BellIcon />
    <span v-if="unreadCount > 0" class="badge">
      {{ unreadCount }}
    </span>
  </router-link>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api';

const unreadCount = ref(0);

onMounted(async () => {
  const response = await api.get('/notifications/unread-count');
  unreadCount.value = response.data.data.count;
});

// Poll every 30 seconds
setInterval(async () => {
  const response = await api.get('/notifications/unread-count');
  unreadCount.value = response.data.data.count;
}, 30000);
</script>
```

### Auto-Refresh Notifications

```javascript
// In NotificationsInbox.vue
const pollInterval = setInterval(() => {
  loadNotifications();
}, 30000); // Every 30 seconds

onUnmounted(() => {
  clearInterval(pollInterval);
});
```

## Production Checklist

- [ ] Configure production SMTP/SendGrid credentials
- [ ] Set `QUEUE_CONNECTION=database` or `redis`
- [ ] Run `php artisan queue:table` and migrate
- [ ] Set up queue workers with supervisor
- [ ] Configure queue failure handling
- [ ] Set up email notification rate limiting
- [ ] Add unsubscribe links to emails
- [ ] Test all notification types in production
- [ ] Set up email monitoring (bounces, complaints)
- [ ] Configure proper `MAIL_FROM_ADDRESS` domain
- [ ] Verify SPF/DKIM records for email domain
- [ ] Add notification preferences (opt-in/opt-out)
- [ ] Set up retry logic for failed emails
- [ ] Monitor queue length and processing time
- [ ] Add notification templates for different languages

## Notification Preferences (Future Enhancement)

Allow users to control which notifications they receive:

```php
// Add to users table migration
$table->json('notification_preferences')->nullable();

// Example preferences
{
  "email": {
    "booking_created": true,
    "booking_status_updated": true,
    "payment_received": true,
    "review_request": false
  },
  "database": {
    "booking_created": true,
    "booking_status_updated": true,
    "payment_received": true,
    "review_request": true
  }
}
```

## Troubleshooting

### Emails Not Sending

1. **Check mail configuration:**
```bash
php artisan config:cache
php artisan config:clear
```

2. **Verify .env settings:**
```bash
php artisan tinker
>>> config('mail.mailers.smtp')
```

3. **Test email sending:**
```bash
php artisan tinker
>>> Mail::raw('Test email', function($msg) { 
     $msg->to('test@example.com')->subject('Test'); 
   });
```

### Notifications Not Appearing

1. **Check database:**
```sql
SELECT * FROM notifications WHERE notifiable_id = 2;
```

2. **Verify user model has Notifiable trait:**
```php
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
}
```

3. **Check API response:**
```bash
curl -H "Authorization: Bearer TOKEN" \
  http://localhost:8000/api/v1/notifications
```

### Queue Not Processing

1. **Check queue connection:**
```bash
php artisan queue:failed
```

2. **Restart queue worker:**
```bash
php artisan queue:restart
php artisan queue:work
```

3. **Check queue table:**
```sql
SELECT * FROM jobs ORDER BY id DESC LIMIT 10;
```

## Support

For notification system issues:
- Email: dev@photographar.com
- Documentation: /docs/EMAIL_NOTIFICATIONS.md
- Laravel Notifications Docs: https://laravel.com/docs/notifications
