# Event Form Dropdowns Fix - Complete Summary

**Date:** February 2, 2026  
**Issue:** City and Photographer dropdowns were not loading data from admin database  
**Status:** ✅ FIXED

---

## Problems Identified

### 1. **City Dropdown Issues**
- API endpoint `/api/v1/admin/cities` returned paginated response with metadata
- Vue component expected flat array of cities
- Missing `minimal` flag for form dropdown use case

### 2. **Photographer Dropdown Issues**
- API endpoint didn't support `status` parameter filtering
- No distinction between different photographer query types (dropdown vs admin list)
- Edit form used manual number input instead of dropdown
- Missing `minimal` flag for form dropdown use case

### 3. **Model Relationship Issues**
- Event model had `organizer()` pointing to User instead of Photographer
- Database schema already had correct foreign keys setup but model relationships were wrong

---

## Solutions Implemented

### A. Backend Changes

#### 1. **CityController.php** - Enhanced `adminIndex()` method
**File:** `app/Http/Controllers/Api/CityController.php`

```php
// Added:
- Check for ?minimal=true or ?minimal=1 query parameter
- Return flat array of cities for dropdowns: [{ id, name, slug, state, division }, ...]
- Keep existing pagination for admin list view
```

**Response for dropdowns:**
```json
{
  "status": "success",
  "data": [
    { "id": 1, "name": "Dhaka", "slug": "dhaka", "state": "Dhaka", "division": null },
    { "id": 2, "name": "Chittagong", "slug": "chittagong", "state": "Chittagong", "division": null }
  ]
}
```

#### 2. **AdminController.php** - Enhanced `getPhotographers()` method
**File:** `app/Http/Controllers/Api/AdminController.php`

```php
// Added:
- NEW: Support for ?status parameter (active, verified, pending)
- Status filtering logic:
  - 'active' = is_verified AND profile_completeness >= 75%
  - 'verified' = is_verified only
  - 'pending' = NOT is_verified
- NEW: Support for ?minimal=true or ?minimal=1 query parameter
- Return minimal data for dropdowns: [{ id, user_id, business_name, user: { id, name, email } }, ...]
```

**Response for dropdowns:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "user_id": 5,
      "business_name": "John Photography",
      "user": { "id": 5, "name": "John Doe", "email": "john@example.com" }
    }
  ]
}
```

#### 3. **Event.php** - Fixed Model Relationships
**File:** `app/Models/Event.php`

```php
// Changed from:
public function organizer()
{
    return $this->belongsTo(User::class, 'organizer_id');
}

// Changed to:
public function organizer()
{
    return $this->belongsTo(Photographer::class, 'organizer_id');
}

public function photographer()
{
    return $this->belongsTo(Photographer::class, 'organizer_id');
}
```

---

### B. Frontend Changes

#### 1. **Create.vue** - City and Photographer Dropdowns
**File:** `resources/js/Pages/Admin/Events/Create.vue`

```javascript
// Updated fetchCities():
const fetchCities = async () => {
  const response = await axios.get('/api/v1/admin/cities?minimal=1', { headers });
  cities.value = response.data.data || [];
};

// Updated fetchPhotographers():
const fetchPhotographers = async () => {
  const response = await axios.get('/api/v1/admin/photographers?status=active&minimal=1', { headers });
  photographers.value = response.data.data || [];
};
```

#### 2. **Edit.vue** - Enhanced with Photographer Dropdown
**File:** `resources/js/Pages/Admin/Events/Edit.vue`

```javascript
// Added photographers ref and fetch function (same as Create.vue)
const photographers = ref([]);

const fetchPhotographers = async () => {
  const response = await axios.get('/api/v1/admin/photographers?status=active&minimal=1', { headers });
  photographers.value = response.data.data || [];
};

// Updated onMounted() to fetch photographers
onMounted(() => {
  fetchCities();
  fetchPhotographers();  // ← NEW
  fetchEvent();
});
```

```vue
<!-- Changed from: -->
<input v-model.number="form.organizer_id" type="number" placeholder="Photographer ID" />

<!-- Changed to: -->
<select v-model="form.organizer_id">
  <option value="">Select photographer</option>
  <option v-for="photographer in photographers" :key="photographer.id" :value="photographer.id">
    {{ photographer.user?.name || photographer.business_name || `Photographer #${photographer.id}` }}
  </option>
</select>
```

---

## API Endpoints Summary

### Cities for Event Forms
```
GET /api/v1/admin/cities?minimal=1
Response: { status, data: [{id, name, slug, state, division}, ...] }
```

### Photographers for Event Forms
```
GET /api/v1/admin/photographers?status=active&minimal=1
Response: { status, data: [{id, user_id, business_name, user: {id, name, email}}, ...] }
```

### Cities for Admin List View (unchanged)
```
GET /api/v1/admin/cities
Response: { status, data: [...paginated cities...], meta: {...} }
```

### Photographers for Admin List View (unchanged, status filter now works)
```
GET /api/v1/admin/photographers
GET /api/v1/admin/photographers?status=verified
GET /api/v1/admin/photographers?status=pending
Response: { status, data: [...photographers...], meta: {...}, stats: {...} }
```

---

## Database Schema (Confirmed Correct)

**Events Table:**
```sql
- id (PRIMARY)
- organizer_id (FOREIGN KEY → photographers.id) ← Uses Photographer, not User
- city_id (FOREIGN KEY → cities.id)
- title, slug, description, ...
```

**Relationships:**
- Event → Photographer (via organizer_id)
- Event → City (via city_id)

---

## Testing Instructions

### 1. **Test City Dropdown**
1. Go to Admin → Events → Create New Event
2. Scroll to "Date, Time & Location" section
3. Click on "City" dropdown
4. Verify cities load from database (should show Dhaka, Chittagong, etc.)
5. Select a city
6. Verify selection is retained

**Expected Result:** 
- ✅ Dropdown populates with cities from database
- ✅ Selection saves when form submitted
- ✅ Saved value reloads on Edit

### 2. **Test Photographer Dropdown**
1. Go to Admin → Events → Create New Event
2. Scroll to "Requirements & Details" section
3. Click on "Organizer/Photographer" dropdown
4. Verify photographers load from database
5. Select a photographer
6. Verify selection is retained

**Expected Result:**
- ✅ Dropdown shows only active photographers (verified + 75%+ profile complete)
- ✅ Names display as: "Photographer Name" or fallback to "Photographer #ID"
- ✅ Selection saves when form submitted
- ✅ Saved value reloads on Edit

### 3. **Test Edit Event**
1. Go to Admin → Events → List
2. Click Edit on any event
3. Verify City dropdown loads with correct selection highlighted
4. Verify Photographer dropdown loads with correct selection highlighted
5. Change city or photographer
6. Click "Update" button
7. Verify changes were saved

**Expected Result:**
- ✅ Both dropdowns maintain previously selected values
- ✅ Can change either dropdown and save successfully

### 4. **Test Draft Saving**
1. Go to Admin → Events → Create New Event
2. Fill only Title, Status (set to Draft)
3. Leave City and Photographer empty
4. Click "Save Draft"
5. Verify event saved

**Expected Result:**
- ✅ Can save draft without required fields
- ✅ Draft status bypasses validation for City and Photographer

### 5. **Test API Endpoints Directly**
```bash
# Test cities endpoint (minimal)
curl "http://localhost:8000/api/v1/admin/cities?minimal=1" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Test photographers endpoint (minimal + status)
curl "http://localhost:8000/api/v1/admin/photographers?status=active&minimal=1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Caching Cleared

All Laravel caches have been cleared:
- ✅ Bootstrap cache
- ✅ Config cache
- ✅ Route cache
- ✅ Event cache
- ✅ View cache

Frontend has been rebuilt:
- ✅ npm run build completed successfully
- ✅ All Vue components compiled

---

## Files Modified

1. **Backend (PHP/Laravel):**
   - `app/Http/Controllers/Api/CityController.php` - Added minimal flag
   - `app/Http/Controllers/Api/AdminController.php` - Added status filtering + minimal flag
   - `app/Models/Event.php` - Fixed Photographer relationship

2. **Frontend (Vue 3):**
   - `resources/js/Pages/Admin/Events/Create.vue` - Updated fetch calls
   - `resources/js/Pages/Admin/Events/Edit.vue` - Added photographers, converted to dropdown

3. **Built Artifacts:**
   - `public/build/` - Rebuilt after Vue component changes

---

## Validation Rules

### City Field
- Required when status ≠ 'draft'
- Must exist in `cities.id`
- Optional for draft events

### Photographer/Organizer Field
- Required when status ≠ 'draft'
- Must exist in `photographers.id`
- Optional for draft events
- Shown as user_id in form, saves as organizer_id

---

## Root Cause Analysis

**Why Dropdowns Were Empty Before:**

1. **City Dropdown:** 
   - API returned pagination wrapper `{ data: {...}, meta: {...} }`
   - Vue tried to iterate `response.data.data` which was the pagination object, not array

2. **Photographer Dropdown:**
   - API didn't support `status` parameter
   - Edit form used number input instead of dropdown

3. **Model Relationship:**
   - Event.organizer pointed to User instead of Photographer
   - This mismatch didn't cause immediate errors due to Laravel's flexible relationship loading
   - But could cause type errors downstream

---

## Next Steps (Optional Enhancements)

- [ ] Add search/autocomplete to photographer dropdown for large lists
- [ ] Add search/autocomplete to city dropdown for large lists
- [ ] Add "quick add" button to create photographers from event form
- [ ] Add "quick add" button to create cities from event form
- [ ] Add pagination to photographer selector
- [ ] Cache photographer/city lists in localStorage for faster form loading

---

## Rollback Plan

If issues occur, revert these files:
1. `app/Http/Controllers/Api/CityController.php` - Revert to previous adminIndex()
2. `app/Http/Controllers/Api/AdminController.php` - Revert getPhotographers()
3. `app/Models/Event.php` - Revert organizer relationship to User
4. `resources/js/Pages/Admin/Events/Create.vue` - Revert fetch functions
5. `resources/js/Pages/Admin/Events/Edit.vue` - Revert to number input
6. Run: `npm run build && php artisan optimize:clear`

---

## Verification Checklist

- [x] CityController minimal flag implemented
- [x] AdminController status filtering implemented
- [x] AdminController minimal flag implemented
- [x] Event model relationships fixed
- [x] Create.vue city fetch updated
- [x] Create.vue photographer fetch updated
- [x] Edit.vue photographers array added
- [x] Edit.vue photographer fetch added
- [x] Edit.vue UI changed to dropdown
- [x] Edit.vue onMounted updated
- [x] Frontend rebuilt successfully
- [x] Caches cleared
- [x] Database schema verified

---

**Status: Ready for Testing ✅**
