# 📋 PHOTOGRAPHER VERIFICATION SYSTEM - QUICK REFERENCE

## 🎯 SYSTEM OVERVIEW

A complete photographer verification workflow allowing photographers to request verification, admins to approve/reject, and verified photographers to display a trusted badge across the platform.

---

## 📁 KEY FILES CREATED

### Backend (12 files)
| File | Purpose | Lines |
|------|---------|-------|
| VerificationRequest.php | Core model | 150 |
| UserVerification.php | Verification status | 100 |
| VerificationController.php | Photographer endpoints | 220 |
| Admin/VerificationController.php | Admin endpoints | 280 |
| VerificationRequestPolicy.php | Authorization | 70 |
| StoreVerificationRequest.php | Form validation | 35 |
| ApproveVerificationRequest.php | Approval validation | 20 |
| RejectVerificationRequest.php | Rejection validation | 23 |
| VerificationRequestSubmitted.php | Notification | 30 |
| VerificationApproved.php | Notification | 30 |
| VerificationRejected.php | Notification | 30 |
| Migrations (2) | Database schema | 60 |

### Frontend (4 files)
| File | Purpose |
|------|---------|
| VerifiedBadge.vue | Reusable badge component |
| Verification/Index.vue | Photographer dashboard |
| Verification/Create.vue | Submission form |
| Admin/Verifications/Index.vue | Admin listing |

---

## 🔑 KEY FEATURES

### 1. Photographer Workflow
```
Visit /verification 
  ↓
Click "Start Verification"
  ↓
Select Type (phone/nid/business)
  ↓
Fill Form + Upload Documents
  ↓
Submit Request
  ↓
Wait for Admin Review (2-3 days)
  ↓
Get Approved → Badge Appears!
```

### 2. Admin Workflow
```
Visit /admin/verifications
  ↓
Filter/Search Pending Requests
  ↓
Click Review
  ↓
View Documents
  ↓
Approve or Reject with Reason
  ↓
Photographer Notified
```

### 3. Badge System
```
When Approved:
  ✓ Badge appears on profile
  ✓ Badge on photographer cards
  ✓ Badge in search results
  ✓ Photographer ranks higher
```

---

## 📊 DATABASE SCHEMA

### verification_requests
```sql
- id (PK)
- user_id (FK) - photographer
- type (enum: phone, nid, business)
- full_name, phone, nid_number, business_name
- document_front_path, document_back_path, selfie_path
- note, status (pending/approved/rejected)
- admin_note, reviewed_by_user_id, reviewed_at
- timestamps (created_at, updated_at)
```

### user_verifications
```sql
- id (PK)
- user_id (FK, unique)
- is_verified (boolean)
- verified_at, verified_by_user_id
- verification_level (basic/full)
- timestamps
```

---

## 🔐 AUTHORIZATION

```
Photographer:
  ✓ Can view own verification dashboard
  ✓ Can create verification request
  ✓ Can view own request details
  ✓ Can download own documents

Admin:
  ✓ Can view all verification requests
  ✓ Can filter and search
  ✓ Can approve pending requests
  ✓ Can reject with reason
  ✓ Can download documents
  ✓ Can view statistics

Super Admin:
  ✓ All admin permissions
  ✓ Can delete requests
```

---

## 📧 NOTIFICATIONS

| Event | Recipient | Channels | Content |
|-------|-----------|----------|---------|
| Request Submitted | Photographer | Email + DB | Confirmation, ETA |
| Approved | Photographer | Email + DB | Congratulations, Badge info |
| Rejected | Photographer | Email + DB | Reason, Reapply link |

All notifications are **async** (queued) for non-blocking delivery.

---

## 🛣️ ROUTES

### Photographer (5 routes)
```
GET    /verification                          → index (dashboard)
GET    /verification/create                   → create (form)
POST   /verification                          → store (submit)
GET    /verification/{id}                     → show (view request)
GET    /verification/{id}/download/{type}    → downloadDocument
```

### Admin (5 routes)
```
GET    /admin/verifications                          → index (list)
GET    /admin/verifications/{id}                     → show (detail)
POST   /admin/verifications/{id}/approve             → approve
POST   /admin/verifications/{id}/reject              → reject
GET    /admin/verifications/{id}/download/{type}     → downloadDocument
GET    /admin/verifications/statistics/get           → statistics API
```

---

## 🎨 COMPONENT USAGE

### Display Verified Badge
```vue
<template>
  <VerifiedBadge 
    :is-verified="photographer.verification?.is_verified"
    :verification-level="photographer.verification?.verification_level"
  />
</template>
```

### In Controller
```php
public function show(Photographer $photographer)
{
    return inertia('Photographer/Show', [
        'photographer' => $photographer->load('verification'),
        'is_verified' => $photographer->verification?->isVerified()
    ]);
}
```

---

## 🔒 SECURITY

### File Upload Security
```
✓ Validate mime types (jpg, png, pdf only)
✓ Validate file size (max 10MB)
✓ Store in private disk (not public)
✓ Serve via signed routes
✓ Authorization checks on download
```

### Data Protection
```
✓ Policy-based authorization
✓ Form request validation
✓ Immutable audit trail
✓ Admin review tracking
✓ Foreign key constraints
```

---

## 🚀 DEPLOYMENT

### 1. Migrate Database
```bash
php artisan migrate
```

### 2. Clear Caches
```bash
php artisan cache:clear
php artisan route:clear
```

### 3. Build Assets
```bash
npm run build
```

### 4. Start Queue
```bash
php artisan queue:work
```

---

## ✅ VERIFICATION TYPES

| Type | Documents | Use Case |
|------|-----------|----------|
| **Phone** | Phone only | Quick verification |
| **NID** | National ID (front + back) + Selfie | Most secure |
| **Business** | Business docs + Selfie | Full business verification |

---

## 📈 RANKING ALGORITHM

```php
// Photographers listed in this order:
ORDER BY 
  is_verified DESC,      // Verified first
  rating DESC,           // Then by rating
  created_at DESC        // Then by recent
```

**Result:** Verified photographers always appear first!

---

## 🔍 FILTERING CAPABILITIES

Admin panel supports:
- Status: pending, approved, rejected
- Type: phone, nid, business
- Search: name, email, phone
- Date range: from, to

---

## 📞 API FOR STATISTICS

```
GET /admin/verifications/statistics/get
```

Returns:
```json
{
  "pending": 5,
  "approved": 42,
  "rejected": 3,
  "total": 50,
  "this_week": 8,
  "avg_review_time": 24
}
```

---

## 🎓 COMMON TASKS

### Approve a Request
```php
$request = VerificationRequest::find($id);
$request->approve(auth()->user(), 'Optional note');
// Automatically:
// - Updates user_verifications table
// - Sends approval notification
// - User can now use verified badge
```

### Reject a Request
```php
$request = VerificationRequest::find($id);
$request->reject(auth()->user(), 'Reason for rejection');
// Automatically:
// - Stores rejection reason
// - Sends rejection notification
// - User can resubmit
```

### Check if Verified
```php
// In controller
if ($photographer->verification?->isVerified()) {
    // Show verified badge
}

// In Blade
@if($photographer->verification?->isVerified())
    <span class="verified-badge">✓</span>
@endif
```

---

## 🐛 TROUBLESHOOTING

| Issue | Solution |
|-------|----------|
| Files won't upload | Check storage permissions: `chmod -R 755 storage/` |
| Notifications not sent | Ensure queue worker is running: `php artisan queue:work` |
| Routes not found | Clear routes: `php artisan route:clear` |
| Badge not showing | Load relationship: `with('verification')` |

---

## 📚 DOCUMENTATION

- **Full Details:** `VERIFICATION_SYSTEM_COMPLETE.md`
- **Deployment:** `VERIFICATION_DEPLOYMENT_TESTING.md`
- **This Guide:** `VERIFICATION_QUICK_REFERENCE.md`

---

## 🎉 STATUS

✅ **PRODUCTION READY**

All components implemented, tested, and ready to deploy!

---

**Last Updated:** February 4, 2026  
**System:** Photographer SB  
**Module:** Photographer Verification v1.0
