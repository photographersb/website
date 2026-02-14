# 🎯 Admin Competition CRUD Operations Guide

**Platform:** Photographer SB  
**Module:** Competition Management  
**Status:** ✅ FULLY IMPLEMENTED  
**Access Level:** Admin, Super Admin, Moderator

---

## 📋 Overview

Admins have **complete CRUD (Create, Read, Update, Delete) control** over all competitions on the platform. This means they can:

- ✅ **CREATE** - Launch new competitions
- ✅ **READ** - View and list all competitions
- ✅ **UPDATE** - Modify competition details, rules, prizes, judges
- ✅ **DELETE** - Remove competitions (with safeguards)

---

## 🔐 Access Control

### Authorized Roles
- `admin` - Full platform administration
- `super_admin` - Complete system control
- `moderator` - Moderation and competition management

### Unauthorized Roles (403 Forbidden)
- `photographer` - Can only view public competitions
- `judge` - Can only view assigned competitions
- `user` - No access

---

## 1️⃣ CREATE - Launch New Competitions

### Endpoint
```
POST /api/v1/admin/competitions
Authorization: Bearer {token}
```

### Required Request Body
```json
{
  "title": "2026 Spring Photography Championship",
  "slug": "spring-2026-championship",
  "description": "Annual spring photography competition showcasing emerging talent",
  "theme": "Nature & Landscape",
  "category_id": 1,
  "submission_deadline": "2026-03-31T23:59:59Z",
  "voting_start_at": "2026-04-01T00:00:00Z",
  "voting_end_at": "2026-04-15T23:59:59Z",
  "judging_start_at": "2026-04-16T00:00:00Z",
  "judging_end_at": "2026-04-30T23:59:59Z",
  "results_announcement_date": "2026-05-01T00:00:00Z",
  "allow_public_voting": true,
  "allow_judge_scoring": true,
  "status": "draft",
  "is_public": false,
  "is_featured": false,
  "participation_fee": 25.00,
  "is_paid_competition": true,
  "max_submissions_per_user": 5,
  "min_submissions_to_proceed": 10,
  "number_of_winners": 3,
  "rules": "All photos must be original work. No AI-generated images allowed.",
  "terms_and_conditions": "Submission implies agreement to all terms.",
  "prizes": [
    {
      "position": 1,
      "title": "First Place",
      "amount": 500,
      "description": "$500 cash prize"
    },
    {
      "position": 2,
      "title": "Second Place",
      "amount": 300,
      "description": "$300 cash prize"
    },
    {
      "position": 3,
      "title": "Third Place",
      "amount": 200,
      "description": "$200 cash prize"
    }
  ],
  "sponsor_ids": [1, 2, 3],
  "judge_ids": [5, 7, 9],
  "total_prize_pool": 1000
}
```

### Optional Fields
```json
{
  "hero_image": "data:image/jpeg;base64,...",
  "banner_image": "data:image/jpeg;base64,...",
  "allow_watermark": true,
  "require_watermark": false
}
```

### Response (201 Created)
```json
{
  "status": "success",
  "message": "Competition created successfully",
  "data": {
    "id": 42,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "title": "2026 Spring Photography Championship",
    "slug": "spring-2026-championship",
    "status": "draft",
    "submissions_count": 0,
    "prizes": [
      {
        "id": 1,
        "rank": 1,
        "title": "First Place",
        "cash_amount": 500
      },
      {
        "id": 2,
        "rank": 2,
        "title": "Second Place",
        "cash_amount": 300
      },
      {
        "id": 3,
        "rank": 3,
        "title": "Third Place",
        "cash_amount": 200
      }
    ],
    "judges": [
      {
        "id": 1,
        "judge_id": 5,
        "judge": {
          "id": 5,
          "name": "Dr. Photography Expert",
          "email": "expert@example.com"
        }
      }
    ]
  }
}
```

---

## 2️⃣ READ - View & List Competitions

### List All Competitions (Admin View)
```
GET /api/v1/admin/competitions
Authorization: Bearer {token}
```

### Query Parameters
```
?status=draft                    # Filter by status
?category_id=1                   # Filter by category
?featured=true                   # Show only featured
?search=championship             # Search by title, slug, theme
?sort_field=created_at           # Sort by field
?sort_direction=desc             # asc or desc
?per_page=20                     # Items per page
?page=1                          # Page number
```

### Response (200 OK)
```json
{
  "status": "success",
  "data": [
    {
      "id": 42,
      "title": "2026 Spring Photography Championship",
      "slug": "spring-2026-championship",
      "status": "draft",
      "submissions_count": 0,
      "prizes": [...],
      "sponsors": [...],
      "category": {...},
      "created_at": "2026-02-03T10:30:00Z",
      "updated_at": "2026-02-03T10:30:00Z"
    }
  ],
  "meta": {
    "total": 15,
    "per_page": 20,
    "current_page": 1,
    "last_page": 1
  }
}
```

### View Single Competition
```
GET /api/v1/admin/competitions/{id}
Authorization: Bearer {token}
```

### Response (200 OK)
```json
{
  "status": "success",
  "data": {
    "id": 42,
    "title": "2026 Spring Photography Championship",
    "description": "Annual spring photography competition...",
    "theme": "Nature & Landscape",
    "submission_deadline": "2026-03-31T23:59:59Z",
    "voting_start_at": "2026-04-01T00:00:00Z",
    "status": "draft",
    "is_public": false,
    "is_featured": false,
    "participation_fee": 25.00,
    "max_submissions_per_user": 5,
    "number_of_winners": 3,
    "prizes": [...],
    "judges": [...],
    "sponsorRecords": [...],
    "submissions_count": 0,
    "created_at": "2026-02-03T10:30:00Z"
  }
}
```

---

## 3️⃣ UPDATE - Modify Competition Details

### Endpoint
```
PUT /api/v1/admin/competitions/{id}
Authorization: Bearer {token}
```

### Updatable Fields
```json
{
  "title": "Updated Title",
  "description": "Updated description",
  "theme": "Updated Theme",
  "status": "published",
  "is_public": true,
  "is_featured": true,
  "featured_until": "2026-03-31T23:59:59Z",
  "submission_deadline": "2026-03-31T23:59:59Z",
  "voting_start_at": "2026-04-01T00:00:00Z",
  "voting_end_at": "2026-04-15T23:59:59Z",
  "judging_start_at": "2026-04-16T00:00:00Z",
  "judging_end_at": "2026-04-30T23:59:59Z",
  "results_announcement_date": "2026-05-01T00:00:00Z",
  "allow_public_voting": true,
  "allow_judge_scoring": true,
  "participation_fee": 30.00,
  "is_paid_competition": true,
  "max_submissions_per_user": 10,
  "min_submissions_to_proceed": 5,
  "number_of_winners": 3,
  "rules": "Updated rules text",
  "terms_and_conditions": "Updated terms",
  "total_prize_pool": 1200
}
```

### Update Prizes
```json
{
  "prizes": [
    {
      "position": 1,
      "title": "Grand Prize",
      "amount": 600,
      "description": "Updated prize"
    }
  ]
}
```

### Update Judges
```json
{
  "judge_ids": [5, 7, 9, 11, 13]
}
```

### Update Sponsors
```json
{
  "sponsor_ids": [1, 2, 3, 4]
}
```

### Response (200 OK)
```json
{
  "status": "success",
  "message": "Competition updated successfully",
  "data": {
    "id": 42,
    "title": "Updated Title",
    "status": "published",
    "is_public": true,
    "is_featured": true,
    "prizes": [...],
    "judges": [...],
    "sponsorRecords": [...]
  }
}
```

### Error Response (422 Unprocessable Entity)
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "submission_deadline": ["The submission deadline must be a valid date"],
    "number_of_winners": ["Number of winners must be at least 1"]
  }
}
```

---

## 4️⃣ DELETE - Remove Competitions

### Endpoint
```
DELETE /api/v1/admin/competitions/{id}
Authorization: Bearer {token}
```

### Response - If Competition Has Submissions (200 OK)
```json
{
  "status": "success",
  "message": "Competition has submissions, so it was archived instead of deleted.",
  "data": {
    "id": 42,
    "title": "2026 Spring Photography Championship",
    "status": "archived",
    "is_public": false,
    "is_featured": false
  }
}
```

**Note:** Competitions with submissions are automatically archived (not deleted) to preserve data integrity.

### Response - If Competition Can Be Deleted (200 OK)
```json
{
  "status": "success",
  "message": "Competition deleted successfully",
  "data": {
    "id": 42,
    "title": "2026 Spring Photography Championship",
    "deleted_at": "2026-02-03T10:30:00Z"
  }
}
```

### When Competition is Deleted
- ✅ All associated prizes are removed
- ✅ Sponsor records are detached
- ✅ Judge assignments are removed
- ✅ Soft delete is performed (recoverable)

### When Competition is Archived (Safe Delete)
- ✅ Status changed to `archived`
- ✅ Set to private (`is_public = false`)
- ✅ Removed from featured (`is_featured = false`)
- ✅ All data preserved for reporting

### Cannot Delete If
- ✗ Competition has submissions
- ✗ Competition is published
- ✗ Winners are announced
- ✗ Certificates generated

---

## 🔄 Competition Statuses

| Status | Description | Can Create Submissions | Can Vote | Can Delete |
|--------|-------------|------------------------|----------|-----------|
| `draft` | Not yet published | ❌ | ❌ | ✅ |
| `published` | Accepting submissions | ✅ | Configured | ⚠️ Archived |
| `closed` | No longer accepting submissions | ❌ | ✅ | ⚠️ Archived |
| `judging` | Winners being evaluated | ❌ | ❌ | ⚠️ Archived |
| `results_announced` | Winners announced | ❌ | ❌ | ⚠️ Archived |
| `completed` | All processes finished | ❌ | ❌ | ⚠️ Archived |
| `archived` | Removed from active list | ❌ | ❌ | ⚠️ Archived |
| `cancelled` | Cancelled by admin | ❌ | ❌ | ⚠️ Archived |

---

## 📊 Additional Admin Operations

### Calculate Winners
```
POST /api/v1/admin/competitions/{id}/calculate-winners
```

### Announce Winners
```
POST /api/v1/admin/competitions/{id}/announce-winners
```

### Generate Certificates
```
POST /api/v1/admin/competitions/{id}/generate-certificates
```

### Set Prizes
```
POST /api/v1/admin/competitions/{id}/set-prize
Body: { "submission_id": 123, "amount": 500, "description": "$500 cash prize" }
```

### View Leaderboard
```
GET /api/v1/admin/competitions/{id}/leaderboard
```

### Get Prize Report
```
GET /api/v1/admin/competitions/{id}/prize-report
```

---

## ✅ Validation Rules

### Create/Update Rules
| Field | Rule | Example |
|-------|------|---------|
| `title` | Required, max 255 chars | "Spring Photography 2026" |
| `slug` | Required, unique, lowercase | "spring-2026" |
| `description` | Required, min 20 chars | "A comprehensive description..." |
| `theme` | Max 100 chars | "Nature & Landscape" |
| `submission_deadline` | Required, future date | "2026-03-31" |
| `number_of_winners` | Min 1, max 10 | 3 |
| `participation_fee` | Min 0, max 10000 | 25.00 |
| `max_submissions_per_user` | Min 1, max 100 | 5 |
| `min_submissions_to_proceed` | Min 1, max 1000 | 10 |

---

## 🛡️ Data Protection

### Soft Deletes
All competitions use soft deletes, meaning:
- Deleted competitions can be restored
- Historical data is preserved
- Relationships are maintained
- Reports remain accurate

### Audit Logging
All CRUD operations are logged:
- Admin who performed action
- What was changed
- When it happened
- Timestamp of action

### Transaction Safety
Complex operations use database transactions:
- If any part fails, entire operation rolls back
- Data consistency maintained
- No partial updates

---

## 🔍 Code Reference

### Controller
📄 File: `app/Http/Controllers/Api/Admin/AdminCompetitionApiController.php`

**Methods:**
- `index()` - List all competitions
- `store()` - Create new competition
- `show()` - Get single competition
- `update()` - Update competition
- `destroy()` - Delete competition

### Models
📄 File: `app/Models/Competition.php`

**Relationships:**
- `admin()` - Who created it
- `organizer()` - Photographer organizing it
- `category()` - Competition category
- `submissions()` - All submissions
- `prizes()` - Prize pool
- `judges()` - Judge assignments
- `sponsorRecords()` - Sponsors

### Routes
📄 File: `routes/api.php` (lines 390-394)

```php
Route::get('/competitions', [AdminCompetitionApiController::class, 'index']);
Route::post('/competitions', [AdminCompetitionApiController::class, 'store']);
Route::get('/competitions/{id}', [AdminCompetitionApiController::class, 'show']);
Route::put('/competitions/{id}', [AdminCompetitionApiController::class, 'update']);
Route::delete('/competitions/{id}', [AdminCompetitionApiController::class, 'destroy']);
```

---

## 📱 Frontend Integration

### Admin Dashboard
- Navigate to: `/admin/competitions`
- List view: All competitions with filters
- Create: "New Competition" button
- Edit: Click competition to modify
- Delete: Confirmation dialog before removal

### Competition Management
- View all statuses
- Search by title or theme
- Filter by category
- Sort by date, title, status
- Bulk operations available

---

## ⚠️ Common Scenarios

### Scenario 1: Create and Publish Competition
```
1. POST /api/v1/admin/competitions (status: "draft")
2. Upload images and set dates
3. PUT /api/v1/admin/competitions/{id} (status: "published")
4. Photographers can now submit
```

### Scenario 2: Edit Published Competition
```
1. GET /api/v1/admin/competitions/{id} (verify status)
2. PUT /api/v1/admin/competitions/{id} (update allowed fields)
3. Note: submission_deadline cannot be moved past
```

### Scenario 3: Cancel Competition Before Results
```
1. GET /api/v1/admin/competitions/{id} (check submissions count)
2. PUT /api/v1/admin/competitions/{id} (status: "cancelled")
3. Notify participants
```

### Scenario 4: Archive After Results
```
1. POST /api/v1/admin/competitions/{id}/calculate-winners
2. POST /api/v1/admin/competitions/{id}/announce-winners
3. POST /api/v1/admin/competitions/{id}/generate-certificates
4. PUT /api/v1/admin/competitions/{id} (status: "archived")
```

---

## 🎓 Summary

**Admins Can:**
- ✅ Create unlimited competitions
- ✅ Manage all competition details
- ✅ Add/remove judges and sponsors
- ✅ Configure prizes and rules
- ✅ Publish to photographers
- ✅ View all submissions
- ✅ Announce winners
- ✅ Generate certificates
- ✅ Archive or delete competitions
- ✅ View detailed reports

**System Guarantees:**
- 🔒 Data integrity with transactions
- 🔐 Role-based access control
- 📝 Complete audit trails
- 🛡️ Soft deletes for recovery
- 🔄 Automatic status management

---

**Status:** ✅ FULLY IMPLEMENTED & TESTED  
**Last Updated:** February 3, 2026
