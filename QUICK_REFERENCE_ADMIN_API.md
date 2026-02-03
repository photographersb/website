# Quick Reference - Admin System API

## Admin Dashboard

### Get Dashboard Data
```bash
curl GET "http://localhost:8000/api/v1/admin/dashboard" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response:** All dashboard stats, recent competitions, platform health, notices

**Query Parameters:**
- `?refresh=1` - Bypass cache and get fresh data

---

## Notice Management

### Create Notice (Admin Only)
```bash
curl -X POST "http://localhost:8000/api/v1/admin/notices" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Notice Title",
    "message": "Notice message",
    "priority": "normal", # low, normal, high, urgent
    "status": "published", # draft, published, archived
    "publish_at": "2026-02-01T00:00:00Z",
    "expires_at": "2026-03-01T00:00:00Z",
    "icon": "bell",
    "color": "blue",
    "show_to_all_roles": false,
    "roles": ["admin", "photographer"] # target roles
  }'
```

### List All Notices (Admin Only)
```bash
curl "http://localhost:8000/api/v1/admin/notices?status=published" \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

**Query Parameters:**
- `?status=draft|published|archived`
- `?priority=low|normal|high|urgent`
- `?search=keyword`

### Get Single Notice (Admin)
```bash
curl "http://localhost:8000/api/v1/admin/notices/1" \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

### Update Notice (Admin)
```bash
curl -X PUT "http://localhost:8000/api/v1/admin/notices/1" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Title",
    "status": "published"
  }'
```

### Delete Notice (Admin)
```bash
curl -X DELETE "http://localhost:8000/api/v1/admin/notices/1" \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

### Get Available Roles
```bash
curl "http://localhost:8000/api/v1/admin/notices/roles/available" \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

### Get My Notices (Any User)
```bash
curl "http://localhost:8000/api/v1/notices/my-notices" \
  -H "Authorization: Bearer USER_TOKEN"
```

**Returns:** Notices visible to user's role, published, not expired

### Mark Notice as Read (Any User)
```bash
curl -X POST "http://localhost:8000/api/v1/notices/1/read" \
  -H "Authorization: Bearer USER_TOKEN"
```

---

## SEO Meta Management

### Get SEO Meta for Entity
```bash
curl "http://localhost:8000/api/v1/admin/seo?model_type=Photographer&model_id=1" \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

**Query Parameters:**
- `model_type` - Model class name (Photographer, Competition, Event)
- `model_id` - ID of the entity

### Create/Update SEO Meta (Admin)
```bash
curl -X POST "http://localhost:8000/api/v1/admin/seo" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "model_type": "Photographer",
    "model_id": 1,
    "meta_title": "Professional Wedding Photographer",
    "meta_description": "Book expert wedding photography",
    "meta_keywords": "wedding,photographer,professional",
    "canonical_url": "https://photographersb.com/photographer/john-doe",
    "og_title": "Wedding Photography Services",
    "og_description": "Professional wedding photos",
    "og_image": "https://cdn.example.com/image.jpg",
    "robots_index": true,
    "robots_follow": true,
    "robots_snippet": "max-snippet-length:160"
  }'
```

### Auto-Generate SEO Meta
```bash
curl -X POST "http://localhost:8000/api/v1/admin/seo/generate" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "model_type": "Photographer",
    "model_id": 1
  }'
```

**Auto-generates from model data:**
- Title from photographer name
- Description from bio
- Canonical URL from slug
- Schema.org JSON-LD for professional service

### Preview SEO Meta (Search Result Preview)
```bash
curl -X POST "http://localhost:8000/api/v1/admin/seo/preview" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "model_type": "Photographer",
    "model_id": 1
  }'
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "title": "Professional Wedding Photographer",
    "description": "Book expert wedding photography services",
    "url": "https://photographersb.com/photographer/john-doe",
    "og_image": "https://cdn.example.com/image.jpg"
  }
}
```

### Delete SEO Meta
```bash
curl -X DELETE "http://localhost:8000/api/v1/admin/seo" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "model_type": "Photographer",
    "model_id": 1
  }'
```

---

## Available Roles

- `admin` - Administrator
- `super_admin` - Super Administrator
- `moderator` - Moderator
- `photographer` - Photographer
- `organizer` - Event Organizer
- `client` - Client/Customer

---

## Status Codes

- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized (not logged in)
- `403` - Forbidden (not authorized)
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## Common Response Formats

### Success
```json
{
  "status": "success",
  "data": { ... },
  "message": "Operation completed"
}
```

### Error
```json
{
  "status": "error",
  "message": "Error description"
}
```

### Validation Error
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### Paginated
```json
{
  "status": "success",
  "data": [...],
  "meta": {
    "total": 100,
    "per_page": 20,
    "current_page": 1
  }
}
```

---

## Debugging

### Check database
```bash
# Login to MySQL
mysql -u root -p photographar_db

# Check notices
SELECT * FROM notices;
SELECT * FROM notice_role;
SELECT * FROM notice_reads;
SELECT * FROM seo_meta;
```

### Check logs
```bash
# Watch Laravel logs
tail -f storage/logs/laravel.log

# Filter for errors
grep ERROR storage/logs/laravel.log | tail -20
```

### Test endpoint
```bash
# Get token first
TOKEN=$(curl -X POST http://localhost:8000/api/v1/auth/login \
  -d 'email=admin@photographar.com&password=password' | jq -r .data.token)

# Use token
curl http://localhost:8000/api/v1/admin/dashboard \
  -H "Authorization: Bearer $TOKEN"
```

---

## Database Indexes

For optimal performance, the following indexes are in place:

```sql
-- Notices
ALTER TABLE notices ADD INDEX idx_status (status);
ALTER TABLE notices ADD INDEX idx_priority (priority);
ALTER TABLE notices ADD INDEX idx_publish_at (publish_at);
ALTER TABLE notices ADD INDEX idx_expires_at (expires_at);

-- Notice Role
ALTER TABLE notice_role ADD UNIQUE idx_notice_role (notice_id, role);

-- Notice Reads
ALTER TABLE notice_reads ADD UNIQUE idx_notice_user (notice_id, user_id);
ALTER TABLE notice_reads ADD INDEX idx_user_read (user_id, read_at);

-- SEO Meta
ALTER TABLE seo_meta ADD INDEX idx_model (model_type, model_id);
ALTER TABLE seo_meta ADD INDEX idx_canonical (canonical_url);
ALTER TABLE seo_meta ADD INDEX idx_created (created_at);
```

---

## Examples

### Blade Template Usage (SEO)
```blade
<!-- In your layout or page -->
<head>
    @php
        $photographer = App\Models\Photographer::with('seoMeta')->find(1);
    @endphp
    
    <x-seo-meta :meta="$photographer->seoMeta" />
</head>
```

### Model Usage (SEO)
```php
// Auto-generate SEO for photographer
$photographer = Photographer::find(1);
$photographer->generateSeoMeta();

// Update SEO
$photographer->updateSeoMeta([
    'meta_title' => 'Professional Photographer',
    'meta_description' => 'Hire me for your events',
]);

// Get SEO
$seo = $photographer->seoMeta;
echo $seo->getRobotsMetaTag();
```

### Model Usage (Notices)
```php
// Create notice
$notice = Notice::create([
    'title' => 'New Feature Released',
    'message' => 'Check out our new features...',
    'status' => 'published',
    'created_by' => auth()->id(),
]);

// Attach roles
$notice->attachRoles(['photographer', 'organizer']);

// Check if user can see it
if ($notice->isVisibleTo($user)) {
    // User can see this notice
}

// Mark as read
$notice->markAsReadBy($user);
```

---

For full documentation, see: **ADMIN_SYSTEM_COMPLETE.md**
