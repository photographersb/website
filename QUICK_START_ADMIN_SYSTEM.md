# Quick Start Guide - Admin System (For Developers)

## 30-Second Setup

```bash
# Already done! Everything is deployed.
# Just verify:
php artisan migrate:status
php artisan route:list | grep admin
```

## What Was Added

### 3 New Controllers
```
✓ Admin\DashboardController              - Dashboard data
✓ Api\Admin\NoticeController             - Notice management
✓ Api\Admin\SeoMetaController            - SEO metadata
```

### 4 New Models
```
✓ Notice                                  - Notice model
✓ NoticeRole                              - Role pivot
✓ NoticeRead                              - Read tracking
✓ SeoMeta                                 - SEO metadata
```

### 1 New Trait
```
✓ HasSeoMeta                              - Add to any model
```

### 3 New Tables
```
✓ notices                                 - 6 columns + timestamps
✓ notice_role                             - Pivot table
✓ notice_reads                            - Read tracking
✓ seo_meta                                - Polymorphic SEO storage
```

### 11 New API Endpoints
```
✓ GET    /api/v1/admin/dashboard          - Dashboard data
✓ GET    /api/v1/admin/notices            - List notices (admin)
✓ POST   /api/v1/admin/notices            - Create notice
✓ GET    /api/v1/admin/notices/{id}       - Get single notice
✓ PUT    /api/v1/admin/notices/{id}       - Update notice
✓ DELETE /api/v1/admin/notices/{id}       - Delete notice
✓ GET    /api/v1/notices/my-notices       - User's notices
✓ POST   /api/v1/notices/{id}/read        - Mark as read
✓ GET    /api/v1/admin/seo                - Get SEO
✓ POST   /api/v1/admin/seo                - Create/update SEO
✓ POST   /api/v1/admin/seo/generate       - Auto-generate SEO
```

---

## Common Tasks

### Get Dashboard Data
```php
// In your Vue component
const response = await fetch('/api/v1/admin/dashboard', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    }
});
const data = await response.json();
console.log(data.data.stats);
```

### Create a Notice
```bash
curl -X POST http://localhost:8000/api/v1/admin/notices \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "New Feature",
    "message": "Check our new features",
    "priority": "high",
    "status": "published",
    "show_to_all_roles": false,
    "roles": ["photographer", "organizer"]
  }'
```

### Add SEO to Photographer
```php
// In your code
$photographer = Photographer::find(1);
$photographer->generateSeoMeta();

// Result: SEO meta auto-generated from name, bio, etc.
```

### Get User's Notices
```javascript
// In your Vue component
const notices = await fetch('/api/v1/notices/my-notices', {
    headers: { 'Authorization': `Bearer ${token}` }
});
```

---

## File Locations

### Controllers
```
app/Http/Controllers/Admin/DashboardController.php
app/Http/Controllers/Api/Admin/NoticeController.php
app/Http/Controllers/Api/Admin/SeoMetaController.php
```

### Models
```
app/Models/Notice.php
app/Models/NoticeRole.php
app/Models/NoticeRead.php
app/Models/SeoMeta.php
```

### Traits
```
app/Traits/HasSeoMeta.php
```

### Migrations
```
database/migrations/2026_01_31_000001_create_notices_table.php
database/migrations/2026_01_31_000002_create_seo_meta_table.php
```

### Routes
```
routes/api.php  (lines with notice and seo)
```

### Views
```
resources/views/components/seo-meta.blade.php
```

### Tests
```
tests/Feature/AdminApiTest.js
```

---

## Database Quick Look

### See All Notices
```sql
SELECT id, title, priority, status, show_to_all_roles, 
       publish_at, expires_at FROM notices;
```

### See Notice Roles
```sql
SELECT nr.notice_id, nr.role, n.title 
FROM notice_role nr 
JOIN notices n ON nr.notice_id = n.id;
```

### See Notice Reads
```sql
SELECT nr.notice_id, nr.user_id, nr.read_at,
       u.name, u.email 
FROM notice_reads nr 
JOIN users u ON nr.user_id = u.id;
```

### See SEO Meta
```sql
SELECT model_type, model_id, meta_title, 
       meta_description, is_auto_generated 
FROM seo_meta;
```

---

## Common Errors & Fixes

### Error: "Admin access required"
- Check user role is 'admin' or 'super_admin'
- Verify token is valid
- Re-login if needed

### Error: "Model not found"
- Verify model_id exists
- Check model_type spelling
- Ensure entity hasn't been deleted

### Error: "Validation failed"
- Check all required fields
- Verify date formats (ISO8601)
- Check enum values (priority, status)

### Dashboard Shows Empty
- Check database connection
- Run `php artisan migrate:status`
- Clear cache: `php artisan cache:clear`
- Check logs: `tail storage/logs/laravel.log`

---

## Using HasSeoMeta Trait

### Add to a Model
```php
// In app/Models/YourModel.php
use App\Traits\HasSeoMeta;

class YourModel extends Model {
    use HasSeoMeta;
}
```

### Auto-Generate SEO
```php
$model = YourModel::find(1);
$model->generateSeoMeta();
```

### Update SEO
```php
$model->updateSeoMeta([
    'meta_title' => 'Title',
    'meta_description' => 'Description'
]);
```

### Get SEO
```php
$seo = $model->seoMeta;
echo $seo->meta_title;
```

---

## Response Examples

### Successful Response
```json
{
  "status": "success",
  "data": { "id": 1, "title": "...", ... },
  "timestamp": "2026-01-31T10:30:00Z"
}
```

### Error Response
```json
{
  "status": "error",
  "message": "Notice not found"
}
```

### Validation Error
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "title": ["Title is required"],
    "message": ["Message must be at least 10 characters"]
  }
}
```

### Paginated Response
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

## Testing

### Run Tests
```bash
npm run test tests/Feature/AdminApiTest.js
```

### Test Individual Suite
```bash
npm run test -- --grep "Admin Dashboard"
npm run test -- --grep "Notices API"
npm run test -- --grep "SEO Meta API"
```

---

## Performance Tips

### Cache Dashboard
```javascript
// Cache response for 30 seconds
const cacheKey = 'admin_dashboard';
const cached = sessionStorage.getItem(cacheKey);

if (cached) {
    return JSON.parse(cached);
}

const data = await fetch('/api/v1/admin/dashboard?...');
sessionStorage.setItem(cacheKey, JSON.stringify(data), 30000);
```

### Batch Operations
```bash
# Create multiple notices at once (loop on client)
for (let notice of notices) {
    await createNotice(notice);
}
```

### Use Indexes
```sql
-- All SEO queries use indexes
SELECT * FROM seo_meta WHERE model_type = 'Photographer' AND model_id = 1;

-- All notice queries use indexes
SELECT * FROM notices WHERE status = 'published';
```

---

## Next Steps

1. **Read Full Documentation:** `ADMIN_SYSTEM_COMPLETE.md`
2. **Try the API:** Use cURL or Postman
3. **Add to UI:** Integrate endpoints in Vue components
4. **Test Thoroughly:** Run test suite
5. **Monitor:** Watch logs for errors

---

## Key Resources

| Document | Purpose |
|----------|---------|
| ADMIN_SYSTEM_COMPLETE.md | Full documentation |
| QUICK_REFERENCE_ADMIN_API.md | API reference |
| IMPLEMENTATION_CHECKLIST_ADMIN.md | Implementation steps |
| PROJECT_DELIVERY_SUMMARY.md | Project overview |
| AdminApiTest.js | Test suite |

---

## Need Help?

1. **Check the logs:** `tail -f storage/logs/laravel.log`
2. **Check the docs:** See resources above
3. **Run diagnostics:**
   ```bash
   php artisan migrate:status
   php artisan route:list | grep -E "notice|seo|admin"
   ```
4. **Test database:**
   ```bash
   mysql -u root -p photographar_db
   SHOW TABLES LIKE '%notice%';
   SHOW TABLES LIKE '%seo%';
   ```

---

## TL;DR - The Absolute Essentials

```php
// Get dashboard
GET /api/v1/admin/dashboard

// Manage notices
POST   /api/v1/admin/notices              // Create
GET    /api/v1/admin/notices              // List
PUT    /api/v1/admin/notices/{id}         // Update
DELETE /api/v1/admin/notices/{id}         // Delete

// SEO operations
POST   /api/v1/admin/seo/generate         // Auto-generate
POST   /api/v1/admin/seo/preview          // Preview

// That's it! You're ready to go.
```

---

**Last Updated:** January 31, 2026  
**Status:** Ready for Production
