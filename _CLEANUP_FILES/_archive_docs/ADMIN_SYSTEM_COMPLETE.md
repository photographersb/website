# Admin Dashboard & Notice System - Implementation Guide

## Overview

This document covers the implementation and usage of:
1. **Fixed Admin Dashboard** - Robust data retrieval with fallback UI
2. **Notice System** - Role-based announcements for all users
3. **SEO Meta System** - Unified SEO metadata for all entities

---

## Part 1: Admin Dashboard

### Problem Fixed
- Dashboard was sometimes empty due to data loading errors
- No fallback UI when queries fail
- Missing error logging

### Solution Implemented
- Created `App\Http\Controllers\Admin\DashboardController`
- Added comprehensive error handling with fallback data
- Caching with short TTL (60 seconds) for real-time updates
- Logging all errors for debugging

### API Endpoint
```
GET /api/v1/admin/dashboard
```

### Response Structure
```json
{
  "status": "success",
  "data": {
    "stats": {
      "total_users": 31,
      "active_users": 5,
      "new_users_today": 0,
      "total_photographers": 19,
      "verified_photographers": 15,
      "pending_verifications": 2,
      "total_bookings": 54,
      "pending_bookings": 3,
      "confirmed_bookings": 20,
      "completed_bookings": 30,
      "total_revenue": 45000,
      "total_events": 12,
      "published_events": 10,
      "total_competitions": 8,
      "active_competitions": 2,
      "total_submissions": 125,
      "pending_submissions": 8
    },
    "recent_competitions": [
      {
        "id": 1,
        "title": "Bangladesh Nature Photography Contest 2026",
        "status": "active",
        "total_prize_pool": 50000,
        "submissions_count": 45,
        "start_date": "2026-02-01"
      }
    ],
    "recent_bookings": [...],
    "platform_health": {
      "status": "operational",
      "uptime": "99.9%",
      "active_sessions": 12,
      "database_status": "healthy"
    },
    "notices": [...]
  },
  "timestamp": "2026-01-31T10:30:00Z"
}
```

### Key Features
- **Always returns data** - Falls back to zeros if queries fail
- **No empty state** - Dashboard always shows widgets with proper data or "0"
- **Error recovery** - Logs errors but doesn't crash the dashboard
- **Cache optimization** - 60-second cache for real-time updates
- **Force refresh** - Pass `?refresh=1` to bypass cache

### Usage in Vue Component
```javascript
const dashboardData = await fetch('/api/v1/admin/dashboard', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json',
  }
});
```

---

## Part 2: Notice System

### Database Schema

#### `notices` Table
```sql
- id (primary key)
- title (string)
- message (longText)
- priority (enum: low, normal, high, urgent)
- status (enum: draft, published, archived)
- publish_at (timestamp nullable)
- expires_at (timestamp nullable)
- icon (string nullable) - for UI badge
- color (string nullable) - CSS class
- show_to_all_roles (boolean)
- created_by (foreign key -> users)
- updated_by (foreign key -> users)
- created_at, updated_at
```

#### `notice_role` Table (Pivot)
```sql
- id (primary key)
- notice_id (foreign key -> notices)
- role (string) - admin, super_admin, moderator, photographer, organizer, client
- created_at, updated_at
```

#### `notice_reads` Table (Analytics)
```sql
- id (primary key)
- notice_id (foreign key -> notices)
- user_id (foreign key -> users)
- read_at (timestamp)
```

### Model: `App\Models\Notice`

#### Methods
```php
// Get or create SEO meta
$notice->isActive(): bool
$notice->isVisibleTo(User $user): bool
$notice->attachRoles(array $roles): void
$notice->detachRoles(array $roles = []): void
$notice->markAsReadBy(User $user): void
$notice->isReadBy(User $user): bool
```

### API Endpoints

#### Admin CRUD
```
GET    /api/v1/admin/notices              - List all notices
POST   /api/v1/admin/notices              - Create notice
GET    /api/v1/admin/notices/{id}         - Get single notice
PUT    /api/v1/admin/notices/{id}         - Update notice
DELETE /api/v1/admin/notices/{id}         - Delete notice
GET    /api/v1/admin/notices/roles/available - Get available roles
```

#### User APIs
```
GET    /api/v1/notices/my-notices         - Get user's visible notices
POST   /api/v1/notices/{id}/read          - Mark notice as read
```

### Create Notice Example
```bash
curl -X POST http://localhost:8000/api/v1/admin/notices \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "System Maintenance",
    "message": "Platform will be down for 2 hours tonight",
    "priority": "high",
    "status": "published",
    "publish_at": "2026-02-01T00:00:00Z",
    "expires_at": "2026-02-01T05:00:00Z",
    "icon": "wrench",
    "color": "red",
    "show_to_all_roles": false,
    "roles": ["admin", "super_admin", "photographer"]
  }'
```

### Features
- **Role-based targeting** - Notice appears only to specified roles
- **Time-based publishing** - Schedule notices for future publication
- **Expiration dates** - Notices auto-expire
- **Read tracking** - Track which users have read notices
- **Priority levels** - Visual indicators for importance

---

## Part 3: SEO Meta System

### Database Schema

#### `seo_meta` Table (Polymorphic)
```sql
- id (primary key)
- model_type (string) - 'Photographer', 'Competition', 'Event', etc.
- model_id (bigint) - ID of the entity
- meta_title (string)
- meta_description (text)
- meta_keywords (string)
- canonical_url (string)
- og_title (string)
- og_description (text)
- og_image (string)
- twitter_card (string)
- twitter_title (string)
- twitter_description (text)
- twitter_image (string)
- robots_index (boolean, default: true)
- robots_follow (boolean, default: true)
- robots_snippet (string nullable)
- schema_json (longText) - JSON-LD for search engines
- created_by (bigint nullable)
- updated_by (bigint nullable)
- is_auto_generated (boolean)
- created_at, updated_at
```

### Model: `App\Models\SeoMeta`

#### Methods
```php
// Get the owning model
$seoMeta->model(): MorphTo

// Get robots meta tag
$seoMeta->getRobotsMetaTag(): string

// Generate schema.org JSON
SeoMeta::generateSchema('photographer', $data): array
SeoMeta::generateSchema('event', $data): array
SeoMeta::generateSchema('competition', $data): array
SeoMeta::generateSchema('article', $data): array
```

### Trait: `App\Traits\HasSeoMeta`

Add this trait to any model to enable SEO meta:

```php
use App\Traits\HasSeoMeta;

class Photographer extends Model {
    use HasSeoMeta;
}
```

#### Trait Methods
```php
// Get or create SEO meta
$model->seoMeta(): MorphOne
$model->getOrCreateSeoMeta(): SeoMeta

// Auto-generate or update
$model->generateSeoMeta(): void
$model->updateSeoMeta(array $data = [], bool $autoGenerate = true): SeoMeta
```

### API Endpoints

```
GET    /api/v1/admin/seo                  - Get SEO meta for entity
POST   /api/v1/admin/seo                  - Create/update SEO meta
POST   /api/v1/admin/seo/generate         - Auto-generate SEO meta
POST   /api/v1/admin/seo/preview          - Preview as search result
DELETE /api/v1/admin/seo                  - Delete SEO meta
```

### Get SEO Meta Example
```bash
curl "http://localhost:8000/api/v1/admin/seo?model_type=Photographer&model_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Update SEO Meta Example
```bash
curl -X POST http://localhost:8000/api/v1/admin/seo \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "model_type": "Photographer",
    "model_id": 1,
    "meta_title": "Professional Wedding Photographer in Dhaka",
    "meta_description": "Expert wedding photography services in Dhaka with 10+ years experience",
    "meta_keywords": "wedding photographer, dhaka, professional photography",
    "canonical_url": "https://photographersb.com/photographer/tamim-iqbal",
    "og_image": "https://cdn.example.com/photographer.jpg",
    "robots_index": true,
    "robots_follow": true
  }'
```

### Auto-Generate SEO Example
```bash
curl -X POST http://localhost:8000/api/v1/admin/seo/generate \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "model_type": "Photographer",
    "model_id": 1
  }'
```

### Preview SEO Example
```bash
curl -X POST http://localhost:8000/api/v1/admin/seo/preview \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "model_type": "Photographer",
    "model_id": 1
  }'
```

### Schema.org Auto-Generation

The system auto-generates schema.org JSON-LD for different entity types:

#### Photographer Schema
```json
{
  "@context": "https://schema.org",
  "@type": "ProfessionalService",
  "name": "Tamim Iqbal",
  "description": "Professional photographer...",
  "image": "avatar.jpg",
  "url": "https://photographersb.com/photographer/tamim",
  "telephone": "+880123456789",
  "email": "tamim@example.com",
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": 4.8,
    "reviewCount": 25
  }
}
```

#### Event Schema
```json
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "Photography Workshop 2026",
  "startDate": "2026-02-15",
  "endDate": "2026-02-16"
}
```

#### Competition Schema
```json
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "Bangladesh Nature Photography Contest",
  "offers": {
    "@type": "Offer",
    "price": "50000",
    "priceCurrency": "BDT"
  }
}
```

### Blade Component for Rendering

Use in your templates:

```blade
<head>
    @php
        $photographer = App\Models\Photographer::with('seoMeta')->find(1);
    @endphp
    
    <x-seo-meta :meta="$photographer->seoMeta" />
</head>
```

The component renders:
- Meta tags (title, description, keywords)
- Open Graph tags (for social media)
- Twitter Card tags
- Canonical URL
- Robots directives
- Schema.org JSON-LD

---

## Included Models

### App\Models\Notice
- Handles notice creation and management
- Role-based visibility logic
- Read tracking

### App\Models\NoticeRole
- Pivot model for notice-role relationships
- Many-to-many between notices and roles

### App\Models\NoticeRead
- Tracks which users have read which notices
- For analytics and "unread count"

### App\Models\SeoMeta
- Stores SEO metadata for all entities
- Handles schema.org generation
- Supports polymorphic relationships

---

## Controllers

### Admin\DashboardController
- `index()` - Get comprehensive dashboard data
- `refreshCache()` - Force cache refresh

### Api\Admin\NoticeController
- `index()` - List notices (admin)
- `store()` - Create notice
- `show()` - Get single notice
- `update()` - Update notice
- `destroy()` - Delete notice
- `getMyNotices()` - Get user's visible notices
- `markAsRead()` - Mark notice as read
- `getRoles()` - Get available roles

### Api\Admin\SeoMetaController
- `show()` - Get SEO meta
- `store()` - Create/update SEO meta
- `generate()` - Auto-generate SEO meta
- `preview()` - Preview as search result
- `destroy()` - Delete SEO meta

---

## Seeders

### NoticeSeeder
Adds 5 sample notices for different roles:
- Admin welcome notice
- Verification reminder
- Maintenance notice
- Competition announcement
- Event management tips

### SeoMetaSeeder
Auto-generates SEO metadata for:
- First 5 photographers
- First 5 competitions

Run seeders:
```bash
php artisan db:seed --class=NoticeSeeder
php artisan db:seed --class=SeoMetaSeeder
```

---

## Testing

### Run Feature Tests
```bash
npm run test tests/Feature/AdminApiTest.js
```

### Test Coverage
1. Admin Dashboard loading
2. Dashboard stats present
3. Notice creation (admin)
4. Role-based notice visibility
5. Mark notice as read
6. SEO meta CRUD operations
7. Auto-generation of SEO
8. SEO preview rendering

---

## Best Practices

### Dashboard
- Always check `dashboardData.is_fallback` to show warning if using fallback data
- Implement refresh button for manual cache clear
- Cache responses on client side for 30 seconds

### Notices
- Always publish with `publish_at` set to now or future
- Set appropriate `expires_at` for time-limited notices
- Use role-based targeting for relevant announcements
- Include icons and colors for visual distinction

### SEO
- Always call `generateSeoMeta()` after creating/editing entities
- Don't manually edit SEO for auto-generated entries (set `is_auto_generated = false` first)
- Preview SEO before publishing to check search appearance
- Use canonical URLs to prevent duplicate content issues

---

## Troubleshooting

### Dashboard shows empty
1. Check logs: `storage/logs/laravel.log`
2. Verify database connection
3. Check if admin user exists and has correct role
4. Try `?refresh=1` parameter to bypass cache

### Notices not appearing
1. Check notice `status` = 'published'
2. Verify `publish_at` <= now()
3. Verify `expires_at` is null or >= now()
4. Check user role matches notice roles
5. Test with `show_to_all_roles` = true first

### SEO meta not saving
1. Verify model has `use HasSeoMeta` trait
2. Check `created_by` and `updated_by` are set
3. Ensure `model_type` matches classname
4. Verify `schema_json` is valid JSON

---

## Future Enhancements

1. Notice read statistics dashboard
2. Email notifications for published notices
3. Notice scheduling with background jobs
4. SEO redirect management for slug changes
5. Multi-language SEO meta support
6. Open Graph image auto-generation
7. Rich text editor for notice messages
8. Notice template system
