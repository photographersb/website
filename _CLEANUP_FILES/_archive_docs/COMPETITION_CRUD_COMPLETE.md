# Competitions Module - Complete CRUD Implementation

## Summary of Changes

This document outlines the complete overhaul of the Competitions module with full CRUD operations, public dashboard improvements, and proper authorization.

---

## 1. Public Dashboard (Requirement A) ✅

### File: `app/Http/Controllers/Api/CompetitionController.php`

**Changes Made:**
- ✅ Fixed `index()` method to show only **published** competitions (not `is_public` flag)
- ✅ Default pagination: **12 per page** (configurable via `per_page` parameter)
- ✅ Default sorting: **Featured competitions first**, then newest (`featured-newest`)
- ✅ Eager loads relationships: admin, organizer, prizes, sponsors
- ✅ Supports filtering: category, search, is_paid status
- ✅ Alternative sorting: deadline, prize pool, submission count, newest

**API Endpoint:**
```bash
GET /api/v1/competitions?page=1&per_page=12&sort=featured-newest
```

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 12)
- `sort`: Sort option (featured-newest, deadline, prize, submissions, newest)
- `category`: Filter by theme/category
- `search`: Search in title/theme/description
- `is_paid`: Filter by paid/free competitions (true/false)

**Response Example:**
```json
{
  "status": "success",
  "data": [
    {
      "id": "uuid",
      "title": "Product Photography 2026",
      "slug": "product-photography-2026",
      "status": "published",
      "is_featured": true,
      "total_prize_pool": 50000,
      "submission_deadline": "2026-03-15",
      "prizes": [...],
      "sponsors": [...]
    }
  ],
  "meta": {
    "total": 25,
    "per_page": 12,
    "current_page": 1,
    "last_page": 3
  }
}
```

---

## 2. Public Detail Page by Slug (Requirement E) ✅

### File: `app/Http/Controllers/Api/CompetitionController.php`

**Changes Made:**
- ✅ Added status check in `show()` method - only published competitions visible
- ✅ Route model binding using **slug** (already configured in Competition model)
- ✅ Returns 404 if draft/archived/not found
- ✅ Eager loads prizes, sponsors, and top 10 submissions

**API Endpoint:**
```bash
GET /api/v1/competitions/{slug}
```

**Example:**
```bash
GET /api/v1/competitions/product-photography-2026
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": "uuid",
    "slug": "product-photography-2026",
    "title": "Product Photography 2026",
    "theme": "Commercial & Product",
    "description": "...",
    "status": "published",
    "is_featured": true,
    "total_prize_pool": 50000,
    "prizes": [...],
    "sponsors": [...],
    "submissions": [...]
  }
}
```

---

## 3. Admin CRUD Operations (Requirement B) ✅

### File: `app/Http/Controllers/Api/Admin/AdminCompetitionApiController.php`

**Authorization:** ✅ Added middleware to check `role IN ('admin', 'super_admin')`

#### 3.1 List Competitions (INDEX)
```bash
GET /api/v1/admin/competitions?page=1&per_page=20&sort_field=created_at&sort_direction=desc&search=&status=published
```

**Query Parameters:**
- `page`: Page number
- `per_page`: Items per page (default: 20)
- `sort_field`: Sort by field (created_at, title, status, etc.)
- `sort_direction`: asc or desc
- `search`: Search by title, slug, or description
- `status`: Filter by status (published, draft, archived)

**Response:**
```json
{
  "status": "success",
  "data": [...],
  "stats": {
    "total": 25,
    "published": 20,
    "draft": 5,
    "archived": 0,
    "featured": 10
  },
  "meta": {
    "total": 25,
    "per_page": 20,
    "current_page": 1,
    "last_page": 2
  }
}
```

#### 3.2 Show Single Competition (SHOW)
```bash
GET /api/v1/admin/competitions/{id}
```

#### 3.3 Create Competition (STORE)
```bash
POST /api/v1/admin/competitions
```

**Request Body:**
```json
{
  "title": "Product Photography 2026",
  "description": "Showcase your best product photography...",
  "theme": "Commercial & Product",
  "submission_deadline": "2026-03-15T23:59:59Z",
  "voting_start": "2026-03-16T00:00:00Z",
  "voting_end": "2026-04-16T23:59:59Z",
  "announcement_date": "2026-04-23T00:00:00Z",
  "status": "published",
  "is_featured": true,
  "total_prize_pool": 50000,
  "max_submissions_per_user": 5,
  "allow_public_voting": true,
  "allow_judge_scoring": true,
  "prizes": [
    {
      "position": 1,
      "title": "Grand Prize",
      "cash_amount": 20000,
      "description": "Winner of this competition"
    }
  ],
  "sponsors": [
    {
      "name": "Canon Bangladesh",
      "tier": "platinum",
      "website_url": "https://canon.com.bd",
      "description": "Camera manufacturer",
      "contribution_amount": 20000
    }
  ]
}
```

**Validation Rules:**
- `title`: required, unique, max 255 chars
- `slug`: auto-generated from title if not provided
- `description`: required
- `theme`: required
- `submission_deadline`: required, must be in future
- `status`: enum (published, draft, archived)
- `is_featured`: boolean
- `total_prize_pool`: numeric, min 0
- `max_submissions_per_user`: integer, 1-10
- `allow_public_voting`: boolean
- `allow_judge_scoring`: boolean
- Nested `prizes` and `sponsors` arrays with validation

#### 3.4 Update Competition (UPDATE)
```bash
PUT /api/v1/admin/competitions/{id}
```

**Request Body:** Same as CREATE, but all fields are optional

**Note:** 
- Slug uniqueness is validated excluding the current competition
- If prizes/sponsors provided, they replace existing ones (delete old, insert new)

#### 3.5 Delete Competition (DESTROY)
```bash
DELETE /api/v1/admin/competitions/{id}
```

**Constraints:**
- Cannot delete if competition has submissions
- Response: HTTP 422 with message to archive instead
- Deletes all related prizes and sponsors

---

## 4. Validation & Authorization (Requirement B) ✅

### Files:
- `app/Http/Requests/CompetitionStoreRequest.php`
- `app/Http/Requests/CompetitionUpdateRequest.php`

**Features:**
- ✅ Comprehensive validation for all fields
- ✅ Auto-slug generation from title (slugifies and ensures uniqueness)
- ✅ Admin authorization check before validation
- ✅ Unique constraint handling for updates
- ✅ Date validation (future dates for submission_deadline)
- ✅ Nested validation for prizes and sponsors
- ✅ Boolean field handling

**Slug Generation:**
```php
// Auto-generates slug from title if not provided
// Example: "Product Photography 2026" → "product-photography-2026"
// Ensures uniqueness: "product-photography-2026-1" if already exists
```

---

## 5. Database & Seeding (Requirement F) ✅

### File: `database/seeders/CompetitionSeeder.php`

**What Gets Seeded:**
- ✅ 8-10 demo competitions with various statuses
- ✅ Includes **"product-photography-2026"** competition
- ✅ Prize distribution for each competition (40-30-20-10%)
- ✅ Sponsor data with tier system (platinum, gold, silver, bronze)
- ✅ Category data for some competitions
- ✅ Sample submission counts and statuses

**To Run Seeder:**
```bash
# Specific seeder
php artisan db:seed --class=CompetitionSeeder

# All seeders
php artisan migrate:fresh --seed
```

**Competitions Created:**
1. **Product Photography 2026** (featured, published)
2. **Portrait Mastery Challenge** (featured, published)
3. **Landscape & Nature Photography** (featured, published)
4. **Street Photography Series** (published)
5. **Wedding & Events** (featured, published)
6. **Wildlife Photography** (published)
7. **Black & White Photography** (published)
8. **Food Photography Pro** (featured, published)
9. **Mobile Photography Challenge** (published)
10. **Macro Photography** (published)

---

## 6. Route Structure (Requirement C) ✅

### API Routes Structure:

```
/api/v1/
├── Public Routes
│   ├── GET    /competitions                     → List all published (12 per page, featured first)
│   ├── GET    /competitions/stats               → Dashboard statistics
│   ├── GET    /competitions/{slug}              → Show detail by slug
│   ├── GET    /competitions/{slug}/leaderboard  → Competition leaderboard
│   ├── GET    /competitions/{slug}/winners      → Winners list
│   └── ...other public endpoints
│
└── Protected Routes (auth:sanctum)
    └── /admin (role:admin|super_admin)
        ├── GET    /competitions                 → List all (admin view, 20 per page)
        ├── POST   /competitions                 → Create new
        ├── GET    /competitions/{id}            → Show details
        ├── PUT    /competitions/{id}            → Update
        └── DELETE /competitions/{id}            → Delete
```

---

## 7. Database Fields (Requirement D) ✅

### Competition Table Structure:
```sql
CREATE TABLE competitions (
    id CHAR(36) PRIMARY KEY,
    admin_id BIGINT UNSIGNED,
    uuid VARCHAR(36) UNIQUE,
    slug VARCHAR(255) UNIQUE,
    title VARCHAR(255),
    description LONGTEXT,
    theme VARCHAR(255),
    status ENUM('draft', 'published', 'archived'),
    is_featured BOOLEAN,
    is_public BOOLEAN,
    is_paid_competition BOOLEAN,
    participation_fee DECIMAL(10,2),
    total_prize_pool DECIMAL(12,2),
    max_submissions_per_user INTEGER,
    allow_public_voting BOOLEAN,
    allow_judge_scoring BOOLEAN,
    submission_deadline TIMESTAMP,
    voting_start_at TIMESTAMP,
    voting_end_at TIMESTAMP,
    judging_start_at TIMESTAMP,
    judging_end_at TIMESTAMP,
    results_announcement_date TIMESTAMP,
    published_at TIMESTAMP,
    featured_until TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 8. Testing API Endpoints

### Example 1: Get Public Competition List
```bash
curl -X GET "http://localhost/api/v1/competitions?page=1&per_page=12&sort=featured-newest"
```

### Example 2: Get Competition by Slug
```bash
curl -X GET "http://localhost/api/v1/competitions/product-photography-2026"
```

### Example 3: Admin - List All Competitions
```bash
curl -X GET "http://localhost/api/v1/admin/competitions?page=1&per_page=20&status=published" \
  -H "Authorization: Bearer {token}"
```

### Example 4: Admin - Create Competition
```bash
curl -X POST "http://localhost/api/v1/admin/competitions" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "New Photography Challenge",
    "description": "Show your best work",
    "theme": "General",
    "submission_deadline": "2026-12-31T23:59:59Z",
    "voting_start": "2027-01-01T00:00:00Z",
    "voting_end": "2027-01-08T23:59:59Z",
    "announcement_date": "2027-01-15T00:00:00Z",
    "status": "published",
    "is_featured": true,
    "total_prize_pool": 100000,
    "max_submissions_per_user": 5
  }'
```

### Example 5: Admin - Update Competition
```bash
curl -X PUT "http://localhost/api/v1/admin/competitions/{id}" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"is_featured": false}'
```

### Example 6: Admin - Delete Competition
```bash
curl -X DELETE "http://localhost/api/v1/admin/competitions/{id}" \
  -H "Authorization: Bearer {token}"
```

---

## 9. Implementation Checklist

- [x] **Requirement A:** Competition Dashboard loads all published competitions
  - [x] Proper status filtering
  - [x] 12 per page pagination
  - [x] Featured-first sorting
  - [x] Search and category filters
  - [x] Eager loads relationships

- [x] **Requirement B:** Admin CRUD with full operations
  - [x] Index (list with filtering/sorting)
  - [x] Show (single competition details)
  - [x] Store (create with validation)
  - [x] Update (edit with FormRequest)
  - [x] Destroy (delete with constraints)
  - [x] Authorization checks

- [x] **Requirement C:** Proper route structure with middleware
  - [x] Public routes for unauthenticated users
  - [x] Admin routes under /admin prefix
  - [x] Auth middleware applied
  - [x] Role-based authorization

- [x] **Requirement D:** Database fields present
  - [x] slug for URL routing
  - [x] status enum for visibility control
  - [x] is_featured boolean for sorting
  - [x] All required dates (submission, voting, announcement)
  - [x] Total prize pool and max submissions

- [x] **Requirement E:** Public detail page by slug
  - [x] Route model binding configured
  - [x] Status check (only published)
  - [x] Eager loads relationships
  - [x] Proper 404 handling

- [x] **Requirement F:** Seeder with demo data
  - [x] 10+ demo competitions created
  - [x] Prize distribution included
  - [x] Sponsor data seeded
  - [x] Includes "product-photography-2026"

- [x] **Requirement G:** UI display ready
  - [x] All necessary fields in API response
  - [x] Image fields (banner_image, hero_image) in models
  - [x] Status and featured badges available
  - [x] Relationships eager loaded

---

## 10. File Changes Summary

### Created Files:
1. `app/Http/Requests/CompetitionStoreRequest.php` - Validation for creating competitions
2. `app/Http/Requests/CompetitionUpdateRequest.php` - Validation for updating competitions

### Modified Files:
1. `app/Http/Controllers/Api/CompetitionController.php`
   - Fixed `index()` to show published competitions
   - Added status check in `show()`
   - Proper pagination and sorting

2. `app/Http/Controllers/Api/Admin/AdminCompetitionApiController.php`
   - Added admin authorization middleware
   - Integrated FormRequest validation
   - Full CRUD implementation

3. `database/seeders/CompetitionSeeder.php`
   - Updated with proper status/published field
   - Demo competitions with prizes and sponsors

### No Changes Needed:
- Routes are already properly configured
- Model relationships already defined
- Database migrations already exist

---

## 11. Next Steps

### To Deploy This Update:
1. Pull the latest code changes
2. Run migration if needed: `php artisan migrate`
3. Seed demo data: `php artisan db:seed --class=CompetitionSeeder`
4. Test endpoints using examples in Section 8
5. Update frontend to use new endpoints

### Common Issues & Solutions:
- **"Unauthenticated" on admin endpoints:** Make sure you're sending valid Bearer token
- **"Unauthorized" on admin endpoints:** Check user role is 'admin' or 'super_admin'
- **404 on detail page:** Verify competition slug is correct and status is 'published'
- **Slug already exists:** Slug is auto-generated and made unique by adding -1, -2, etc.

---

## 12. Support

For questions or issues, refer to:
- [API_QUICK_REFERENCE.md](./API_QUICK_REFERENCE.md) - API documentation
- [QUICK_START_GUIDE.md](./QUICK_START_GUIDE.md) - General quick start
- Competition model: `app/Models/Competition.php`

---

**Last Updated:** 2025
**Status:** ✅ Complete - All 7 requirements implemented
