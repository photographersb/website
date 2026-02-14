# BOOKING MARKETPLACE QUICK REFERENCE

## Core Entities & Relationships

```
BookingRequest (core booking entity)
├── belongsTo(User, 'client_user_id')
├── belongsTo(User, 'photographer_user_id')
├── belongsTo(Category)
├── hasMany(BookingMessage)          → Messaging thread
└── hasMany(BookingStatusLog)        → Audit trail

BookingMessage (messaging within booking)
├── belongsTo(BookingRequest)
└── belongsTo(User, 'sender_user_id')

BookingStatusLog (audit trail - immutable)
├── belongsTo(BookingRequest)
└── belongsTo(User, 'changed_by_user_id')
```

## Status Flow

```
┌──────────┐
│ PENDING  │  Initial state when client creates booking
└────┬─────┘
     │
     ├─→ ACCEPTED ─→ COMPLETED  (photographer fulfilled booking)
     │              \→ CANCELLED (either party cancels)
     │
     ├─→ DECLINED    (photographer rejects)
     │
     └─→ CANCELLED   (client cancels before acceptance)
```

## API Endpoints (by Route Name)

### Client Endpoints
```
GET    booking.create              /@{username}/book
POST   booking.store               /bookings
GET    booking.show                /bookings/{booking}
GET    booking.client.list         /my-bookings/client
POST   booking.cancel              /bookings/{booking}/cancel
```

### Photographer Endpoints
```
GET    booking.show                /bookings/{booking}
GET    booking.photographer.list   /my-bookings/photographer
POST   booking.accept              /bookings/{booking}/accept
POST   booking.decline             /bookings/{booking}/decline
POST   booking.complete            /bookings/{booking}/complete
```

### Messaging Endpoints
```
POST   booking.message.store       /bookings/{booking}/messages
DELETE booking.message.delete      /messages/{message}
POST   booking.messages.read       /bookings/{booking}/messages/read
```

### Admin Endpoints
```
GET    admin.booking.index         /admin/bookings
GET    admin.booking.show          /admin/bookings/{booking}
POST   admin.booking.cancel        /admin/bookings/{booking}/cancel
POST   admin.booking.dispute       /admin/bookings/{booking}/dispute
GET    admin.booking.statistics    /admin/bookings/statistics/get
```

## Key Methods

### BookingRequest Model
```php
// State checks (boolean)
$booking->isPending()
$booking->isAccepted()
$booking->isDeclined()
$booking->isCancelled()
$booking->isCompleted()

// State validation
$booking->canBeAccepted()       // Photographer can accept?
$booking->canBeDeclined()       // Photographer can decline?
$booking->canBeCancelled()      // Can be cancelled?

// UI helpers
$booking->getStatusBadgeClass()     // CSS class for status
$booking->getUnreadMessageCount()   // Unread messages count
```

### Controller Helpers
```php
// Generate booking code
$code = $this->generateBookingCode(); // Returns: SB-BK-2026-0001

// Create status log
$booking->statusLogs()->create([
    'old_status' => 'pending',
    'new_status' => 'accepted',
    'changed_by_user_id' => auth()->id(),
    'note' => 'Booking accepted by photographer',
]);

// Notify user
$user->notify(new BookingRequestCreated($booking));
```

## Policy Authorization

```php
// Use in controllers
$this->authorize('view', $booking);
$this->authorize('accept', $booking);
$this->authorize('decline', $booking);

// Use in views
@can('accept', $booking)
    <button>Accept Booking</button>
@endcan

// JS/Vue
if (booking.can_accept) {
    // Show accept button
}
```

## Notifications (All Queued + Database + Email)

| Event | Trigger | To | Data |
|-------|---------|----|----|
| BookingRequestCreated | Client creates | Photographer | Code, client name, date, budget |
| BookingAccepted | Photographer accepts | Client | Code, photographer name, date |
| BookingDeclined | Photographer declines | Client | Code, photographer name |
| BookingCancelled | Either cancels | Other party | Code, date, reason |
| BookingCompleted | Photographer completes | Client | Code, photographer name, date |

## Database Queries

```php
// Get client's bookings
$bookings = BookingRequest::forClient(auth()->id())->get();

// Get photographer's bookings
$bookings = BookingRequest::forPhotographer(auth()->id())->get();

// Get pending bookings for a photographer
$pending = BookingRequest::forPhotographer($photoId)
    ->byStatus('pending')
    ->get();

// Get completed bookings with messages
$completed = BookingRequest::where('status', 'completed')
    ->with('messages.sender', 'statusLogs.changedByUser')
    ->get();

// Search bookings
$results = BookingRequest::where('booking_code', 'like', '%SB-BK%')
    ->orWhereHas('client', fn($q) => $q->where('name', 'like', '%search%'))
    ->get();
```

## Frontend Usage

### Booking Form (Create.vue)
```javascript
// Navigate to booking form
route('booking.create', { photographerUsername: 'john_doe' })

// Submit booking
form.post(route('booking.store'), {
    onSuccess: () => console.log('Booking sent!')
})
```

### Booking Detail (Show.vue)
```javascript
// Accept booking
router.post(route('booking.accept', booking.id))

// Decline with reason
router.post(route('booking.decline', booking.id), {
    reason: 'Not available on that date'
})

// Send message
router.post(route('booking.message.store', booking.id), {
    message: 'What about outdoor shots?'
})
```

## Common Patterns

### Creating a Booking (Backend)
```php
$booking = BookingRequest::create([
    'photographer_user_id' => $photoId,
    'client_user_id' => auth()->id(),
    'booking_code' => $this->generateBookingCode(),
    'status' => 'pending',
    'event_date' => $request->event_date,
    // ... other fields
]);

// Log initial status
$booking->statusLogs()->create([
    'new_status' => 'pending',
    'changed_by_user_id' => auth()->id(),
    'note' => 'Booking request created',
]);

// Notify photographer
$booking->photographer->notify(new BookingRequestCreated($booking));
```

### Accepting a Booking (Backend)
```php
$this->authorize('accept', $booking);

$oldStatus = $booking->status;
$booking->update([
    'status' => 'accepted',
    'accepted_at' => now(),
]);

$booking->statusLogs()->create([
    'old_status' => $oldStatus,
    'new_status' => 'accepted',
    'changed_by_user_id' => auth()->id(),
    'note' => 'Booking accepted by photographer',
]);

$booking->client->notify(new BookingAccepted($booking));
```

### Sending a Message (Backend)
```php
$this->authorize('view', $booking);

$booking->messages()->create([
    'sender_user_id' => auth()->id(),
    'message' => $validated['message'],
    'attachment_path' => $attachmentPath,
]);
```

## Error Handling

```php
// Authorization errors
if (! auth()->user()->can('accept', $booking)) {
    abort(403, 'You cannot accept this booking.');
}

// State validation
if (! $booking->isPending()) {
    return back()->withErrors('Booking is not in pending state.');
}

// Validation errors (form requests handle automatically)
// StoreMessageRequest, CreateBookingRequest, CancelBookingRequest
```

## Testing

```bash
# Create test booking
php artisan tinker
>>> $client = User::find(1);
>>> $photo = User::find(2);
>>> $booking = BookingRequest::create([...]);

# Test notifications
>>> $booking->photographer->notify(new BookingRequestCreated($booking));

# Check notifications table
>>> DB::table('notifications')->latest()->first();

# Test status transitions
>>> $booking->update(['status' => 'accepted', 'accepted_at' => now()]);
>>> $booking->statusLogs()->create([...]);
```

## Performance Tips

1. **Eager Load Relationships:** Always use `with()` to avoid N+1
   ```php
   BookingRequest::with('client', 'photographer', 'messages.sender')->get()
   ```

2. **Paginate Large Lists:**
   ```php
   BookingRequest::with('photographer')->paginate(10)
   ```

3. **Queue Notifications:**
   ```php
   // All notifications implement ShouldQueue
   // They'll be processed asynchronously
   ```

4. **Index Frequently Queried Columns:**
   ```
   - client_user_id
   - photographer_user_id
   - status
   - event_date
   - created_at
   ```

## Debugging

```php
// Log booking state
Log::info('Booking status change', [
    'booking_id' => $booking->id,
    'old_status' => $oldStatus,
    'new_status' => $booking->status,
    'changed_by' => auth()->id(),
]);

// Check authorization
dd(auth()->user()->can('accept', $booking));

// Inspect relationships
dd($booking->load('messages.sender', 'statusLogs.changedByUser'));

// View all status logs
dd($booking->statusLogs()->orderBy('created_at', 'desc')->get());
```

---

Generated: 2026-02-05
