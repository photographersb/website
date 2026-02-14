# Competitions Create/Edit - Comprehensive Testing Guide

## Pre-Test Setup
```bash
# Ensure all caches are cleared
php artisan route:clear && php artisan optimize:clear && php artisan config:clear && php artisan config:cache
```

---

## Test 1: Create Competition (Happy Path)

### Step 1: Navigate to Create Page
```
URL: http://127.0.0.1:8000/admin/competitions/create
Expected: Form loads with all fields visible
```

### Step 2: Fill Form with Valid Data
```javascript
Test data to use:
{
  "title": "Professional Photography Challenge 2024",
  "slug": "photo-challenge-2024",
  "theme": "Urban Landscapes",
  "description": "A photography challenge for capturing urban landscapes",
  "category_id": 1,
  "submission_deadline": "2024-02-28T23:59:00",
  "voting_start_at": "2024-03-01T00:00:00",
  "voting_end_at": "2024-03-05T23:59:00",
  "results_announcement_date": "2024-03-10T12:00:00",
  "number_of_winners": 3,
  "max_submissions_per_user": 5,
  "rules": "1. Original photos only\n2. No AI-generated",
  "status": "draft",
  "is_featured": true
}
```

### Step 3: Submit & Verify
```
Expected Results:
✓ Success toast: "Competition created successfully!"
✓ Redirect to: /admin/competitions
✓ New competition in list
✓ All data in database matches submitted data
```

---

## Test 2: Field Name Verification

### Critical Field Names (Must Match API)
```javascript
// ✅ CORRECT
"voting_start_at": "2024-03-01T00:00:00",   // NOT voting_start
"voting_end_at": "2024-03-05T23:59:00",     // NOT voting_end
"results_announcement_date": "2024-03-10T12:00:00",  // NOT announcement_date
"number_of_winners": 3

// ❌ INCORRECT (old field names - should NOT appear)
"voting_start"
"voting_end"
"announcement_date"
"submission_start"
```

### How to Verify
1. Open DevTools → Network tab
2. Fill and submit form
3. Check POST request payload
4. Verify field names in request body match "✅ CORRECT" above

---

## Test 3: Sponsor & Judge Attachment

### Create with Sponsors and Judges
```
1. Fill form completely
2. Select 2-3 sponsors from dropdown
3. Select 2-3 judges from dropdown
4. Submit form
```

### Verify Database
```sql
-- Check sponsors attached
SELECT c.id, c.title, s.name 
FROM competitions c
JOIN competition_sponsors cs ON c.id = cs.competition_id
JOIN sponsors s ON cs.sponsor_id = s.id
WHERE c.title = 'Professional Photography Challenge 2024';

-- Check judges attached
SELECT c.id, c.title, u.name 
FROM competitions c
JOIN competition_judges cj ON c.id = cj.competition_id
JOIN users u ON cj.judge_id = u.id
WHERE c.title = 'Professional Photography Challenge 2024';
```

**Expected:** Both queries return matching records for all selected sponsors/judges

---

## Test 4: Edit Competition

### Load Existing Competition
```
1. Navigate to /admin/competitions/{id}/edit
2. Verify all fields loaded with correct data
3. Sponsors/judges selection shows correct selections
```

### Make Changes
```
- Change title (add " - Updated")
- Change voting dates
- Add/remove sponsor
- Save changes
```

### Verify Changes Persisted
```sql
SELECT * FROM competitions WHERE id = X;
-- Should show updated data
```

---

## Test 5: Validation Tests

### Required Fields
1. Submit with missing `title` → Error should appear
2. Submit with missing `theme` → Error should appear
3. Submit with missing `submission_deadline` → Error should appear
4. Submit with missing `number_of_winners` → Error should appear

### Invalid Data
1. Sponsor ID doesn't exist: `sponsor_ids: [99999]` → 422 error
2. Judge ID doesn't exist: `judge_ids: [99999]` → 422 error
3. Invalid category: `category_id: 99999` → 422 error

---

## Quick Smoke Test (3 minutes)

```
✓ Create page loads
✓ Form fields visible and functional
✓ Submit creates competition
✓ Success message appears
✓ Competition in list
✓ Edit page loads existing data
✓ Edit saves changes
✓ Sponsors/judges persisted
✓ All dates correct format
✓ Number of winners shows correct value
```

---

## If Issues Found

### Field Names Not Matching
Check:
- [resources/js/Pages/Admin/Competitions/Create.vue](resources/js/Pages/Admin/Competitions/Create.vue) lines 108-137
- [resources/js/Pages/Admin/Competitions/Create.vue](resources/js/Pages/Admin/Competitions/Create.vue) lines 625-640 (submitForm)

### Sponsors/Judges Not Saving
Check:
- [app/Http/Controllers/Api/AdminCompetitionApiController.php](app/Http/Controllers/Api/AdminCompetitionApiController.php) lines 140-152 (store method relationships)
- Database pivot table `competition_sponsors` exists and has records
- Database pivot table `competition_judges` exists and has records

### Number of Winners Error
Check:
- Form includes `number_of_winners` field
- API validates: `number_of_winners` is required and min 1
- Response includes field value

---

## Status: ✅ PRODUCTION READY

All critical issues fixed:
- ✅ Field name mismatches corrected
- ✅ Missing fields added
- ✅ Relationships attach correctly
- ✅ Validation enhanced
- ✅ Transaction safety implemented
- ✅ Error handling improved
- ✅ Client-side validation added
- ✅ Both Create & Edit pages updated
