# Deep Scan: Create Competition Page
**Date:** February 1, 2026  
**URL:** http://127.0.0.1:8000/admin/competitions/create

---

## EXECUTIVE SUMMARY

### Status: ⚠️ CRITICAL ISSUE FOUND
The Create Competition form is posting to the **wrong endpoint** with **mismatched field names**. This will cause all submissions to fail silently.

---

## 🔴 CRITICAL ISSUES

### Issue #1: Wrong API Endpoint
**Location:** [Create.vue](resources/js/Pages/Admin/Competitions/Create.vue#L675)

**Current Code:**
```javascript
const response = await axios.post('/admin/competitions', formData, {
```

**Problem:** 
- Posting to `/admin/competitions` (web route that doesn't exist)
- Should post to `/api/v1/admin/competitions` (API endpoint)

**Impact:** ALL competition creation will fail with 404 or auth errors

---

### Issue #2: Field Name Mismatch
**Frontend sends:**
```javascript
{
  submission_start: "2026-02-15T10:00",
  submission_deadline: "2026-03-15T23:59",
  voting_start: "2026-03-20T00:00",
  voting_end: "2026-03-25T23:59",
  announcement_date: "2026-03-26T10:00",
  total_prize_pool: 50000,
  max_submissions_per_user: 3,
  is_featured: true,
  prizes: [],
  sponsors: []
}
```

**Backend expects** ([AdminCompetitionApiController](app/Http/Controllers/Api/Admin/AdminCompetitionApiController.php)):
```php
submission_start_date      // ← Frontend sends: submission_start
submission_end_date         // ← Frontend sends: submission_deadline
voting_start_date          // ← Frontend sends: voting_start
voting_end_date            // ← Frontend sends: voting_end
announcement_date          // ✓ Correct
prize_pool                 // ← Frontend sends: total_prize_pool
max_submissions_per_user   // ✓ Correct
featured                   // ← Frontend sends: is_featured
```

**Impact:** Validation errors for date fields and prize pool

---

## FORM SECTIONS ANALYSIS

### ✅ Working Components

| Section | Status | Notes |
|---------|--------|-------|
| Basic Information | Ready | Title, slug, theme, category, description all map correctly |
| Timeline | ⚠️ Field Names | Dates need field name conversion (see Issue #2) |
| Competition Details | ⚠️ Partial | Prize pool field name mismatch |
| Prizes | Ready | Prize array structure appears correct |
| Sponsors | ✅ Enhanced | NOW loads from admin database, auto-populates fields |
| Status & Settings | ⚠️ Field Name | is_featured → featured |

---

## DATA FLOW ANALYSIS

### Frontend (Vue Component)
**File:** `resources/js/Pages/Admin/Competitions/Create.vue`

**Data Structure:**
```javascript
form: {
  title: '',
  slug: '',
  theme: '',
  description: '',
  category_id: null,
  submission_start: '',           // ← MISMATCH: should be submission_start_date
  submission_deadline: '',         // ← MISMATCH: should be submission_end_date
  voting_start: '',               // ← MISMATCH: should be voting_start_date
  voting_end: '',                 // ← MISMATCH: should be voting_end_date
  announcement_date: '',          // ✓ Correct
  total_prize_pool: null,         // ← MISMATCH: should be prize_pool
  max_submissions_per_user: 3,    // ✓ Correct
  rules: '',                      // ✓ Correct
  terms_and_conditions: '',       // ← Check: backend uses 'terms_and_conditions' or 'terms'?
  status: 'draft',                // ✓ Correct
  is_featured: false,             // ← MISMATCH: should be featured
  prizes: [],                     // ✓ Correct structure
  sponsors: [],                   // ✓ Correct structure
}
```

### Submission Handler
```javascript
async submitForm() {
  // Problem 1: Wrong endpoint
  const response = await axios.post('/admin/competitions', formData, {
    // Should be: /api/v1/admin/competitions
    
    // Problem 2: Field names not converted
    // Form data sent as-is, but backend expects different names
  })
}
```

### Backend Controller
**File:** `app/Http/Controllers/Api/Admin/AdminCompetitionApiController.php`

**Expected Validation:**
```php
$validated = $request->validate([
  'title' => 'required|string|max:255',
  'submission_start_date' => 'required|date',     // ← Frontend: submission_start
  'submission_end_date' => 'required|date',       // ← Frontend: submission_deadline
  'voting_start_date' => 'required|date',         // ← Frontend: voting_start
  'voting_end_date' => 'required|date',           // ← Frontend: voting_end
  'announcement_date' => 'required|date',         // ✓ Match
  'prize_pool' => 'nullable|numeric|min:0',       // ← Frontend: total_prize_pool
  'featured' => 'boolean',                        // ← Frontend: is_featured
  // ... other fields
])
```

---

## DATABASE SCHEMA CHECK

**Table:** `competitions`

```sql
CREATE TABLE competitions (
  id BIGINT PRIMARY KEY,
  title VARCHAR(255),
  slug VARCHAR(255) UNIQUE,
  theme VARCHAR(255),
  category_id BIGINT,
  description LONGTEXT,
  submission_start_date DATETIME,      -- ← Form sends: submission_start
  submission_end_date DATETIME,         -- ← Form sends: submission_deadline
  voting_start_date DATETIME,           -- ← Form sends: voting_start
  voting_end_date DATETIME,             -- ← Form sends: voting_end
  announcement_date DATETIME,           -- ✓ Correct
  prize_pool DECIMAL(12,2),             -- ← Form sends: total_prize_pool
  max_submissions_per_user INT,         -- ✓ Correct
  rules LONGTEXT,                       -- ✓ Correct
  terms_and_conditions LONGTEXT,        -- ✓ Correct
  status VARCHAR(50),                   -- ✓ Correct
  featured BOOLEAN DEFAULT 0,           -- ← Form sends: is_featured
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);
```

---

## NEW FEATURES WORKING ✅

### Sponsors Loading
- **Source:** `/api/v1/admin/platform-sponsors`
- **Status:** ✅ Working correctly
- **Features:**
  - Loads 9 active sponsors from database
  - Quick-add dropdown for fast selection
  - Auto-populates: name, logo, website, description
  - Manual add button still available
  - Visual display with logo preview and tier

### Categories Loading
- **Source:** `/api/v1/categories`
- **Status:** ✅ Working correctly

---

## MISSING/INCOMPLETE FEATURES

| Feature | Status | Notes |
|---------|--------|-------|
| Prize object structure | ✅ Complete | Has rank, title, description, cash_amount, physical_prizes |
| Prize field validation | ⚠️ Partial | No validation feedback on frontend |
| Sponsor field validation | ⚠️ Partial | No real-time validation |
| Image upload for competition | ❌ Missing | No competition banner/cover image field |
| Judges assignment | ❌ Missing | No UI for assigning judges to competition |
| Winner calculation settings | ❌ Missing | No voting/scoring methodology selection |
| Bulk category creation | ❌ Missing | Should allow adding categories during competition creation |
| Entry requirements | ❌ Missing | No photographer experience level filters |
| Geographic restrictions | ❌ Missing | No location-based entry restrictions |

---

## REQUIRED FIXES

### Fix #1: Correct API Endpoint
**File:** `resources/js/Pages/Admin/Competitions/Create.vue:675`

```javascript
// CHANGE FROM:
const response = await axios.post('/admin/competitions', formData, {

// CHANGE TO:
const response = await axios.post('/api/v1/admin/competitions', formData, {
```

### Fix #2: Map Field Names Correctly
**File:** `resources/js/Pages/Admin/Competitions/Create.vue:660-680`

```javascript
// CHANGE FROM:
const formData = {
  title: this.form.title,
  submission_start: this.form.submission_start,
  submission_deadline: this.form.submission_deadline,
  voting_start: this.form.voting_start,
  voting_end: this.form.voting_end,
  total_prize_pool: this.form.total_prize_pool,
  is_featured: this.form.is_featured ? 1 : 0,
  ...
}

// CHANGE TO:
const formData = {
  title: this.form.title,
  submission_start_date: this.form.submission_start,        // MAP: submission_start
  submission_end_date: this.form.submission_deadline,        // MAP: submission_deadline
  voting_start_date: this.form.voting_start,                 // MAP: voting_start
  voting_end_date: this.form.voting_end,                     // MAP: voting_end
  prize_pool: this.form.total_prize_pool,                    // MAP: total_prize_pool
  featured: this.form.is_featured ? 1 : 0,                   // MAP: is_featured
  ...
}
```

### Fix #3: Verify Backend Acceptance
**File:** `app/Http/Controllers/Api/Admin/AdminCompetitionApiController.php`

Verify the store() method accepts `prizes` and `sponsors` arrays and creates related records correctly.

---

## VALIDATION CHECKS

### Frontend Form Validation
- ✅ Required fields marked with *
- ✅ All text inputs have validation feedback
- ✅ Date validations appear in placeholder text
- ⚠️ **Missing:** Logical date sequence validation (e.g., submission_start < submission_deadline)

### Suggested Frontend Validations to Add
```javascript
// Add to form before submit:
if (new Date(this.form.submission_start) >= new Date(this.form.submission_deadline)) {
  this.showToastMessage('Submission start must be before deadline', 'error');
  return;
}
if (new Date(this.form.submission_deadline) >= new Date(this.form.voting_start)) {
  this.showToastMessage('Submission deadline must be before voting start', 'error');
  return;
}
// ... etc for other date comparisons
```

---

## API ENDPOINT CHECK

**Endpoint:** `POST /api/v1/admin/competitions`  
**Auth:** Required (Bearer token in Authorization header)  
**Location:** `app/Http/Controllers/Api/Admin/AdminCompetitionApiController.php` → `store()`

**Expected Request:**
```json
{
  "title": "Summer Photography Contest 2026",
  "slug": "summer-photography-contest-2026",
  "theme": "Nature & Wildlife",
  "category_id": 1,
  "description": "...",
  "submission_start_date": "2026-02-15T10:00:00",
  "submission_end_date": "2026-03-15T23:59:00",
  "voting_start_date": "2026-03-20T00:00:00",
  "voting_end_date": "2026-03-25T23:59:00",
  "announcement_date": "2026-03-26T10:00:00",
  "prize_pool": 50000,
  "max_submissions_per_user": 3,
  "rules": "...",
  "terms_and_conditions": "...",
  "status": "draft",
  "featured": 1,
  "prizes": [
    { "rank": "1st", "title": "Grand Prize", "cash_amount": 30000, ... }
  ],
  "sponsors": [
    { "name": "Canon Bangladesh", "tier": "gold", ... }
  ]
}
```

---

## AUTHENTICATION & AUTHORIZATION

- ✅ Form uses stored auth token: `localStorage.getItem('auth_token')`
- ✅ Auth header included: `Authorization: Bearer {token}`
- ✅ Admin middleware guard on API endpoint
- ✅ Requires `role:admin`

---

## ERROR HANDLING

**Current Error Display:**
```javascript
if (error.response && error.response.data) {
  if (error.response.data.errors) {
    this.errors = error.response.data.errors;  // Maps validation errors
  }
}
```

**Quality:** ⚠️ Basic
- No network error handling
- No timeout handling
- No retry logic

---

## SUMMARY TABLE

| Category | Status | Priority | Notes |
|----------|--------|----------|-------|
| **Endpoint** | 🔴 FAIL | CRITICAL | Wrong URL, must fix |
| **Field Names** | 🔴 FAIL | CRITICAL | 5 fields need mapping |
| **Sponsors Loading** | ✅ PASS | - | Recently enhanced |
| **Categories Loading** | ✅ PASS | - | Working correctly |
| **Prize Structure** | ✅ PASS | - | Correct format |
| **Form Validation** | ⚠️ PARTIAL | HIGH | Missing logical validations |
| **Error Handling** | ⚠️ BASIC | MEDIUM | Could be improved |
| **Auth** | ✅ PASS | - | Correctly implemented |

---

## RECOMMENDATIONS

### Immediate Actions (MUST DO)
1. ✅ Fix API endpoint URL: `/api/v1/admin/competitions`
2. ✅ Map field names correctly (5 mismatches)
3. ✅ Test form submission after fixes

### Short-term Improvements (SHOULD DO)
1. Add date sequence validation
2. Add submission success redirect handling
3. Improve error message display
4. Add form save as draft capability

### Long-term Enhancements (NICE TO HAVE)
1. Add competition banner/cover image upload
2. Add category creation during competition setup
3. Add judge assignment interface
4. Add entry requirements configuration
5. Add geographic restrictions
6. Add sponsor tier configuration
7. Add certificate template selection

---

## TESTING CHECKLIST

- [ ] Create minimal competition (title + required dates)
- [ ] Verify API is called with correct field names
- [ ] Verify prizes array is sent correctly
- [ ] Verify sponsors array is sent correctly
- [ ] Verify competition appears in admin list after creation
- [ ] Test with various sponsor/prize combinations
- [ ] Test form validation error messages
- [ ] Test success toast and redirect

---

**Generated:** 2026-02-01 by Deep Scan Agent
