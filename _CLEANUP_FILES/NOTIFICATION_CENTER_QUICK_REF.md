# Notification Center - Quick Reference

## Access
**URL**: `http://127.0.0.1:8000/admin/notifications`
**Navigation**: Admin Dashboard → Notifications (in sidebar)

## Key Features

### 📊 Dashboard Stats
```
┌──────────┬──────────┬──────────┬──────────┐
│  Total   │  Unread  │  Today   │  Week    │
│   125    │    15    │     8    │    42    │
└──────────┴──────────┴──────────┴──────────┘
```

### 🔍 Filters
- **Type**: All, Booking, Payment, Tip, Review, Verification, Competition, System
- **Status**: All, Unread Only, Read Only
- **Period**: All Time, Today, This Week, This Month

### ⚡ Quick Actions
- **Mark All Read** - Clears all unread badges
- **Delete Read** - Removes all read notifications

### 📋 Notification Card
```
┌─────────────────────────────────────────────┐
│ [📅] BOOKING  NEW                          │
│ New Booking Request                         │
│ John Doe requested a booking for Feb 10     │
│ 5m ago • John Doe              [✓] [🗑️]    │
└─────────────────────────────────────────────┘
```

## API Endpoints

### Admin Routes (requires auth + admin role)
```
GET    /api/v1/admin/notifications
       ?type=booking&status=unread&period=today

POST   /api/v1/admin/notifications/{id}/mark-read
POST   /api/v1/admin/notifications/mark-all-read
DELETE /api/v1/admin/notifications/{id}
DELETE /api/v1/admin/notifications/delete-read
```

## Notification Types & Colors

| Type | Badge | Icon | Color |
|------|-------|------|-------|
| Booking | 📅 | Calendar | Blue |
| Payment | 💳 | Card | Green |
| Tip | 💵 | Dollar | Yellow |
| Review | ⭐ | Star | Purple |
| Verification | ✅ | Check | Indigo |
| Competition | ✨ | Sparkle | Pink |
| System | ℹ️ | Info | Gray |

## Time Format Examples
- `Just now` - Less than 1 minute
- `5m ago` - 5 minutes ago
- `2h ago` - 2 hours ago
- `3d ago` - 3 days ago
- `Feb 6` - More than 7 days

## Common Operations

### Filter by Unread Bookings
1. Select "Booking" in Type dropdown
2. Select "Unread Only" in Status dropdown
3. Notifications auto-reload

### Clean Up Old Notifications
1. Click "Delete Read" button
2. Confirm deletion
3. All read notifications removed

### Mark All as Read
1. Click "Mark All Read" button
2. All notifications marked as read
3. Unread count becomes 0

## Files Location

### Frontend
- Component: `resources/js/Pages/Admin/NotificationCenter.vue`
- Route: `resources/js/app.js` (line ~344)

### Backend
- Controller: `app/Http/Controllers/Api/Admin/NotificationController.php`
- Routes: `routes/api.php` (line ~553)

## Testing URLs
```bash
# View notification center
http://127.0.0.1:8000/admin/notifications

# API endpoints (requires bearer token)
curl http://127.0.0.1:8000/api/v1/admin/notifications
curl -X POST http://127.0.0.1:8000/api/v1/admin/notifications/mark-all-read
```

## Build Command
```bash
npm run build
```

## Troubleshooting

### No notifications showing?
- Check database `notifications` table has data
- Verify user is logged in as admin
- Check browser console for API errors

### Filters not working?
- Clear browser cache
- Rebuild Vue app: `npm run build`
- Check API response in Network tab

### Delete not working?
- Confirm notification belongs to current user
- Check console for 403/500 errors
- Verify route is registered in api.php

## Status
✅ Fully implemented and tested
✅ Build successful (14.92 KB)
✅ All API endpoints working

---
*Quick Reference v1.0 - February 6, 2026*
