# P1 API Testing Guide

## Health Check Endpoints

### Public Health Check
```bash
curl -X GET http://localhost/api/v1/health
```

**Expected Response (200 OK):**
```json
{
  "status": "healthy",
  "timestamp": "2026-02-03T10:30:45.000000Z",
  "database": "ok",
  "cache": "ok",
  "uptime": 45
}
```

### Admin System Health Check
```bash
# Requires authentication (Bearer token)
curl -X GET http://localhost/api/v1/admin/health \
  -H "Authorization: Bearer {YOUR_ADMIN_TOKEN}"
```

**Expected Response (200 OK):**
```json
{
  "status": "system_status",
  "database": "ok",
  "active_users": 42,
  "total_photographers": 14,
  "active_competitions": 3,
  "pending_approvals": 5,
  "failed_jobs": 0,
  "timestamp": "2026-02-03T10:30:45.000000Z"
}
```

---

## Photographer Onboarding Endpoints

### Get Onboarding Checklist
```bash
curl -X GET http://localhost/api/v1/photographer/onboarding/checklist \
  -H "Authorization: Bearer {PHOTOGRAPHER_TOKEN}"
```

**Expected Response (200 OK):**
```json
{
  "status": "success",
  "message": "Onboarding checklist retrieved",
  "data": {
    "checklist": {
      "id": 1,
      "photographer_id": 1,
      "profile_completed": true,
      "profile_photo_uploaded": true,
      "portfolio_added": true,
      "phone_verified": true,
      "city_added": true,
      "years_of_experience_added": true,
      "hourly_rate_set": false,
      "bio_added": true,
      "social_media_added": true,
      "terms_accepted": true,
      "completed_at": null,
      "created_at": "2026-02-03T10:00:00.000000Z",
      "updated_at": "2026-02-03T10:30:00.000000Z"
    },
    "completion_percentage": 90,
    "next_step": {
      "step": "hourly_rate_set",
      "label": "Set Hourly Rate"
    },
    "is_complete": false
  }
}
```

### Update Onboarding Step
```bash
curl -X PUT http://localhost/api/v1/photographer/onboarding/checklist/update-step \
  -H "Authorization: Bearer {PHOTOGRAPHER_TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "step": "hourly_rate_set",
    "completed": true
  }'
```

**Expected Response (200 OK):**
```json
{
  "status": "success",
  "message": "Onboarding step updated",
  "data": {
    "checklist": {...},
    "completion_percentage": 100,
    "next_step": null,
    "is_complete": true
  }
}
```

**Valid Steps:**
- `profile_completed`
- `profile_photo_uploaded`
- `portfolio_added`
- `phone_verified`
- `city_added`
- `years_of_experience_added`
- `hourly_rate_set`
- `bio_added`
- `social_media_added`
- `terms_accepted`

### Get Onboarding Progress
```bash
curl -X GET http://localhost/api/v1/photographer/onboarding/progress \
  -H "Authorization: Bearer {PHOTOGRAPHER_TOKEN}"
```

**Expected Response (200 OK):**
```json
{
  "status": "success",
  "message": "Onboarding progress retrieved",
  "data": {
    "completion_percentage": 100,
    "completed_steps": {
      "id": 1,
      "photographer_id": 1,
      "profile_completed": true,
      "profile_photo_uploaded": true,
      "portfolio_added": true,
      "phone_verified": true,
      "city_added": true,
      "years_of_experience_added": true,
      "hourly_rate_set": true,
      "bio_added": true,
      "social_media_added": true,
      "terms_accepted": true,
      "completed_at": "2026-02-03T10:35:00.000000Z"
    },
    "next_step": null,
    "is_complete": true,
    "completed_at": "2026-02-03T10:35:00.000000Z"
  }
}
```

---

## Admin Onboarding Management

### Get Pending Onboardings
```bash
curl -X GET "http://localhost/api/v1/admin/photographers/onboarding/pending?per_page=20" \
  -H "Authorization: Bearer {ADMIN_TOKEN}"
```

**Expected Response (200 OK):**
```json
{
  "status": "success",
  "message": "Pending onboardings retrieved",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "slug": "john-doe",
      "completion_percentage": 50,
      "onboarding_checklist": {...}
    }
  ],
  "meta": {
    "total": 5,
    "per_page": 20,
    "current_page": 1,
    "last_page": 1
  }
}
```

### Reset Photographer Onboarding
```bash
curl -X POST http://localhost/api/v1/admin/photographers/{photographer_id}/onboarding/reset \
  -H "Authorization: Bearer {ADMIN_TOKEN}" \
  -H "Content-Type: application/json"
```

**Expected Response (200 OK):**
```json
{
  "status": "success",
  "message": "Onboarding checklist reset",
  "data": []
}
```

---

## City API (Updated with ApiResponse)

### Get All Cities
```bash
curl -X GET http://localhost/api/v1/cities
```

**Expected Response (200 OK):**
```json
{
  "status": "success",
  "message": "Cities retrieved successfully",
  "data": [
    {"id": 1, "name": "Dhaka", "slug": "dhaka", ...},
    {"id": 2, "name": "Chittagong", "slug": "chittagong", ...}
  ]
}
```

### Create City (Admin)
```bash
curl -X POST http://localhost/api/v1/admin/cities \
  -H "Authorization: Bearer {ADMIN_TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Sylhet",
    "division": "Sylhet Division",
    "state": "Sylhet"
  }'
```

**Expected Response (201 Created):**
```json
{
  "status": "success",
  "message": "City created successfully",
  "data": {
    "id": 65,
    "name": "Sylhet",
    "slug": "sylhet",
    "division": "Sylhet Division",
    "state": "Sylhet",
    "created_at": "2026-02-03T10:45:00.000000Z"
  }
}
```

### Update City (Admin)
```bash
curl -X PUT http://localhost/api/v1/admin/cities/65 \
  -H "Authorization: Bearer {ADMIN_TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Sylhet Updated",
    "division": "Sylhet Division"
  }'
```

**Expected Response (200 OK):**
```json
{
  "status": "success",
  "message": "City updated successfully",
  "data": {...}
}
```

### Delete City (Admin)
```bash
curl -X DELETE http://localhost/api/v1/admin/cities/65 \
  -H "Authorization: Bearer {ADMIN_TOKEN}"
```

**Expected Response (200 OK):**
```json
{
  "status": "success",
  "message": "City deleted successfully",
  "data": []
}
```

---

## Error Responses

### Validation Error (422)
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "name": ["The name field is required."],
    "city_id": ["The selected city_id is invalid."]
  }
}
```

### Not Found (404)
```json
{
  "status": "error",
  "message": "Resource not found",
  "data": null
}
```

### Unauthorized (401)
```json
{
  "status": "error",
  "message": "Unauthenticated"
}
```

### Forbidden (403)
```json
{
  "status": "error",
  "message": "Not a photographer"
}
```

---

## Testing with Postman

### 1. Create Environment Variables
- `base_url`: http://localhost
- `photographer_token`: {YOUR_PHOTOGRAPHER_TOKEN}
- `admin_token`: {YOUR_ADMIN_TOKEN}

### 2. Sample Requests

**Health Check:**
```
GET {{base_url}}/api/v1/health
```

**Get Checklist:**
```
GET {{base_url}}/api/v1/photographer/onboarding/checklist
Authorization: Bearer {{photographer_token}}
```

**Update Step:**
```
PUT {{base_url}}/api/v1/photographer/onboarding/checklist/update-step
Authorization: Bearer {{photographer_token}}
Content-Type: application/json

{
  "step": "hourly_rate_set",
  "completed": true
}
```

---

## Debugging Tips

### Check Database Directly
```bash
# Connect to database
mysql -u root -p photographar_db

# View onboarding checklists
SELECT * FROM photographer_onboarding_checklists\G

# Check specific photographer
SELECT * FROM photographer_onboarding_checklists 
WHERE photographer_id = 1\G
```

### Check Laravel Logs
```bash
cd "c:\xampp\htdocs\Photographar SB"
tail -f storage/logs/laravel.log
```

### Verify Migration Status
```bash
php artisan migrate:status
```

### Test FormRequest Validation
```bash
# Invalid request - missing required field
curl -X POST http://localhost/api/v1/admin/cities \
  -H "Authorization: Bearer {ADMIN_TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{"division": "Test"}'

# Should return 422 Validation Error
```

---

## Performance Notes

**New Endpoints Query Counts:**
- `GET /api/v1/health` - 1 query
- `GET /api/v1/admin/health` - 4 queries
- `GET /api/v1/photographer/onboarding/checklist` - 2 queries
- `PUT /api/v1/photographer/onboarding/checklist/update-step` - 3 queries
- `GET /api/v1/admin/photographers/onboarding/pending` - 2 queries (paginated)

**Expected Response Times:**
- Health checks: < 50ms
- Onboarding endpoints: < 100ms
- Admin endpoints: < 150ms

---

## Troubleshooting

### Issue: 404 Not Found
**Solution:** Check that routes are registered
```bash
php artisan route:list | grep health
php artisan route:list | grep onboarding
```

### Issue: 401 Unauthorized
**Solution:** Ensure token is valid and includes admin role
```bash
php artisan tinker
>>> \App\Models\User::where('role', 'admin')->first()->createToken('test')->plainTextToken
```

### Issue: 422 Validation Error
**Solution:** Check FormRequest validation rules
```bash
php artisan tinker
>>> new \App\Http\Requests\StoreCityRequest()
```

### Issue: 500 Internal Server Error
**Solution:** Check logs
```bash
tail -50 storage/logs/laravel.log
```

---

## Success Checklist

- [ ] Health check returns 200 OK
- [ ] Admin health check returns system stats
- [ ] Photographer can retrieve onboarding checklist
- [ ] Photographer can update onboarding step
- [ ] Completion percentage calculates correctly
- [ ] Admin can view pending onboardings
- [ ] Admin can reset photographer onboarding
- [ ] City endpoints use ApiResponse trait
- [ ] All error responses are consistent
- [ ] No N+1 queries detected (use Laravel Debugbar)
