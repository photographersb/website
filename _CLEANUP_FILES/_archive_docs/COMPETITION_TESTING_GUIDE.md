# Competitions Module - Quick Testing Guide

## Setup Instructions

### Step 1: Seed Demo Data
```bash
php artisan db:seed --class=CompetitionSeeder
```

### Step 2: Get Admin Authentication Token
1. Login with admin credentials:
```bash
curl -X POST "http://localhost/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@photographar.com",
    "password": "password123"
  }'
```

2. Save the returned `token` for use in admin requests

---

## Quick Test Cases

### Test 1: Public Dashboard - List Competitions
**Purpose:** Verify public users can see all published competitions

```bash
curl -X GET "http://localhost/api/v1/competitions?page=1&per_page=12" \
  -H "Accept: application/json"
```

**Expected Results:**
- ✅ HTTP 200 OK
- ✅ Returns 12 competitions (or less if fewer exist)
- ✅ All have `status: "published"`
- ✅ Featured competitions listed first
- ✅ Includes `prizes` and `sponsors` arrays

---

### Test 2: Public Dashboard - With Sorting
**Purpose:** Verify featured competitions show first

```bash
curl -X GET "http://localhost/api/v1/competitions?page=1&per_page=5&sort=featured-newest" \
  -H "Accept: application/json"
```

**Expected Results:**
- ✅ Featured competitions at top (is_featured: true)
- ✅ Results ordered by newest (created_at DESC)

---

### Test 3: Public Dashboard - Search
**Purpose:** Test search functionality

```bash
curl -X GET 'http://localhost/api/v1/competitions?search=product' \
  -H "Accept: application/json"
```

**Expected Results:**
- ✅ Returns competitions matching "product"
- ✅ Matches in title, theme, or description

---

### Test 4: View Competition Details by Slug
**Purpose:** Verify public can view published competition details

```bash
curl -X GET "http://localhost/api/v1/competitions/product-photography-2026" \
  -H "Accept: application/json"
```

**Expected Results:**
- ✅ HTTP 200 OK
- ✅ Full competition details with slug: "product-photography-2026"
- ✅ Includes all prizes and sponsors
- ✅ Shows top 10 submissions

---

### Test 5: Try to View Draft Competition
**Purpose:** Verify draft competitions are not visible to public

```bash
curl -X GET "http://localhost/api/v1/competitions/portrait-masters-2026" \
  -H "Accept: application/json"
```

**Expected Results:**
- ✅ HTTP 404 Not Found
- ✅ Message: "This competition is not available"

---

### Test 6: Admin - List All Competitions
**Purpose:** Verify admin can see all competitions (published + draft)

```bash
curl -X GET "http://localhost/api/v1/admin/competitions" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Expected Results:**
- ✅ HTTP 200 OK
- ✅ Returns all competitions regardless of status
- ✅ Includes statistics (total, published, draft, featured)
- ✅ Paginated (20 per page by default)

---

### Test 7: Admin - Filter by Status
**Purpose:** Test admin status filtering

```bash
curl -X GET "http://localhost/api/v1/admin/competitions?status=draft" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Expected Results:**
- ✅ Returns only draft competitions
- ✅ Stats show draft count

---

### Test 8: Admin - View Single Competition
**Purpose:** Verify admin can view competition details

```bash
curl -X GET "http://localhost/api/v1/admin/competitions/UUID_HERE" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Expected Results:**
- ✅ HTTP 200 OK
- ✅ Returns full competition with all relationships
- ✅ Includes prizes and sponsors

---

### Test 9: Admin - Create Competition
**Purpose:** Test creating a new competition

```bash
curl -X POST "http://localhost/api/v1/admin/competitions" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Photography Challenge",
    "description": "A test competition",
    "theme": "General",
    "submission_deadline": "2026-12-31T23:59:59Z",
    "voting_start": "2027-01-01T00:00:00Z",
    "voting_end": "2027-01-08T23:59:59Z",
    "announcement_date": "2027-01-15T00:00:00Z",
    "status": "draft",
    "is_featured": false,
    "total_prize_pool": 50000,
    "max_submissions_per_user": 5,
    "allow_public_voting": true,
    "allow_judge_scoring": true
  }'
```

**Expected Results:**
- ✅ HTTP 201 Created
- ✅ Returns new competition with ID
- ✅ Slug auto-generated: "test-photography-challenge"
- ✅ `admin_id` set to current user

---

### Test 10: Admin - Create with Prizes and Sponsors
**Purpose:** Test creating competition with nested data

```bash
curl -X POST "http://localhost/api/v1/admin/competitions" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Advanced Challenge 2026",
    "description": "Advanced photography challenge",
    "theme": "Advanced",
    "submission_deadline": "2026-12-31T23:59:59Z",
    "voting_start": "2027-01-01T00:00:00Z",
    "voting_end": "2027-01-08T23:59:59Z",
    "announcement_date": "2027-01-15T00:00:00Z",
    "status": "published",
    "is_featured": true,
    "total_prize_pool": 100000,
    "max_submissions_per_user": 3,
    "prizes": [
      {
        "position": 1,
        "title": "Grand Prize",
        "cash_amount": 50000,
        "description": "Grand Prize Winner"
      },
      {
        "position": 2,
        "title": "Runner Up",
        "cash_amount": 30000,
        "description": "Second Prize"
      }
    ],
    "sponsors": [
      {
        "name": "Test Camera Co",
        "tier": "platinum",
        "website_url": "https://testcamera.com",
        "description": "Test sponsor",
        "contribution_amount": 50000
      }
    ]
  }'
```

**Expected Results:**
- ✅ HTTP 201 Created
- ✅ Prizes array populated
- ✅ Sponsors array populated

---

### Test 11: Admin - Update Competition
**Purpose:** Test updating an existing competition

```bash
curl -X PUT "http://localhost/api/v1/admin/competitions/UUID_HERE" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "is_featured": true,
    "total_prize_pool": 150000
  }'
```

**Expected Results:**
- ✅ HTTP 200 OK
- ✅ Changes applied
- ✅ Returns updated competition

---

### Test 12: Admin - Delete Competition (No Submissions)
**Purpose:** Test deleting competition without submissions

```bash
curl -X DELETE "http://localhost/api/v1/admin/competitions/UUID_HERE" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Expected Results:**
- ✅ HTTP 200 OK
- ✅ Message: "Competition deleted successfully"

---

### Test 13: Admin - Delete Competition (With Submissions)
**Purpose:** Verify cannot delete competition with submissions

```bash
curl -X DELETE "http://localhost/api/v1/admin/competitions/UUID_WITH_SUBMISSIONS" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Expected Results:**
- ✅ HTTP 422 Unprocessable Entity
- ✅ Message: "Cannot delete competition with existing submissions..."

---

### Test 14: Unauthorized Admin Access
**Purpose:** Verify non-admin cannot access admin endpoints

```bash
curl -X GET "http://localhost/api/v1/admin/competitions" \
  -H "Authorization: Bearer REGULAR_USER_TOKEN" \
  -H "Accept: application/json"
```

**Expected Results:**
- ✅ HTTP 403 Forbidden
- ✅ Message: "Unauthorized. Admin access required."

---

### Test 15: Missing Auth Token
**Purpose:** Verify auth required for admin endpoints

```bash
curl -X GET "http://localhost/api/v1/admin/competitions" \
  -H "Accept: application/json"
```

**Expected Results:**
- ✅ HTTP 401 Unauthorized
- ✅ Message: "Unauthenticated"

---

## Validation Test Cases

### Test V1: Invalid Email Format
```bash
curl -X POST "http://localhost/api/v1/admin/competitions" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"title": "Test", ...emails_invalid...}'
```

**Expected:** HTTP 422 with validation errors

---

### Test V2: Duplicate Title
```bash
curl -X POST "http://localhost/api/v1/admin/competitions" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Product Photography 2026",
    ...other fields...
  }'
```

**Expected:** HTTP 422 - Title already exists

---

### Test V3: Past Submission Deadline
```bash
curl -X POST "http://localhost/api/v1/admin/competitions" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "submission_deadline": "2020-01-01T00:00:00Z",
    ...other fields...
  }'
```

**Expected:** HTTP 422 - Deadline must be in future

---

## Performance Test Cases

### Test P1: Large Pagination
```bash
curl -X GET "http://localhost/api/v1/competitions?page=1&per_page=1000"
```

**Expected:**
- ✅ HTTP 200 OK
- ✅ Returns results (may be limited server-side)

---

### Test P2: Complex Search
```bash
curl -X GET "http://localhost/api/v1/competitions?search=photography&category=portrait&is_paid=true&sort=deadline"
```

**Expected:**
- ✅ Returns matching results
- ✅ Respects all filters

---

## Expected Statistics

After running seeder:
- Total competitions: **10**
- Published: **8-9**
- Draft: **1**
- Featured: **5-6**
- Total prize pool: **~600,000**

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| No results returned | Check database seeded: `php artisan db:seed --class=CompetitionSeeder` |
| 404 on admin routes | Verify token is valid and user has admin role |
| Slug conflicts | Seeder auto-generates unique slugs with -1, -2 suffix |
| Validation errors | Check date formats (ISO 8601) and required fields |
| Token expired | Get new token by logging in again |

---

## Files Modified

1. ✅ `app/Http/Controllers/Api/CompetitionController.php`
2. ✅ `app/Http/Controllers/Api/Admin/AdminCompetitionApiController.php`
3. ✅ `app/Http/Requests/CompetitionStoreRequest.php`
4. ✅ `app/Http/Requests/CompetitionUpdateRequest.php`
5. ✅ `database/seeders/CompetitionSeeder.php`
6. ✅ `COMPETITION_CRUD_COMPLETE.md` (documentation)

---

**Status:** ✅ Ready for Testing

Run tests in order for best results. Each test builds on previous setup.
