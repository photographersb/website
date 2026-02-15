# ⚡ QUICK REFERENCE - Admin CRUD & A4 Certificates

## 🎯 ADMIN CRUD OPERATIONS

### Create Competition
```bash
POST /api/v1/admin/competitions
{
  "title": "Competition Title",
  "status": "draft",
  "prizes": [{position: 1, amount: 500}],
  "judge_ids": [5, 7, 9]
}
```

### List Competitions
```bash
GET /api/v1/admin/competitions?status=published&sort_field=created_at
```

### View Competition
```bash
GET /api/v1/admin/competitions/{id}
```

### Update Competition
```bash
PUT /api/v1/admin/competitions/{id}
{
  "title": "New Title",
  "status": "published"
}
```

### Delete Competition
```bash
DELETE /api/v1/admin/competitions/{id}
# Auto-archives if has submissions
# Soft-deletes if no submissions
```

---

## 🖨️ A4 CERTIFICATE PRINTING

### Certificate Dimensions
- **Size:** A4 Landscape (297mm × 210mm)
- **DPI:** 300 (professional print)
- **Margins:** 0mm (full-bleed)
- **Format:** PDF

### Print Steps
1. **Download:** `/api/v1/certificates/{id}/download`
2. **Open:** In browser or PDF viewer
3. **Print:** Ctrl+P (Windows) or Cmd+P (Mac)
4. **Settings:**
   - Paper: A4
   - Orientation: Landscape
   - Margins: None
   - Scale: 100%
5. **Print** to professional printer

### Certificate Types
- 🥇 **1st Place** - Gold border (#FFD700)
- 🥈 **2nd Place** - Silver border (#C0C0C0)
- 🥉 **3rd Place** - Bronze border (#CD7F32)
- 🏆 **Honorable** - Gray border (#4B5563)

---

## 📂 DOCUMENTATION FILES

| File | Purpose | Lines |
|------|---------|-------|
| `ADMIN_COMPETITION_CRUD_GUIDE.md` | Complete CRUD API reference | 407 |
| `CERTIFICATE_A4_PRINTING_GUIDE.md` | Printing specifications & guide | 371 |
| `certificate-preview.html` | Interactive certificate preview | Interactive |

---

## 🔐 ACCESS CONTROL

### Authorized Roles
- ✅ `admin`
- ✅ `super_admin`
- ✅ `moderator`

### Unauthorized (403 Forbidden)
- ❌ `photographer`
- ❌ `judge`
- ❌ `user`

---

## 📊 COMPETITION STATUSES

| Status | Submissions | Voting | Can Delete |
|--------|-------------|--------|-----------|
| draft | ❌ | ❌ | ✅ |
| published | ✅ | ✅* | ⚠️ |
| closed | ❌ | ✅ | ⚠️ |
| judging | ❌ | ❌ | ⚠️ |
| results_announced | ❌ | ❌ | ⚠️ |
| completed | ❌ | ❌ | ⚠️ |
| archived | ❌ | ❌ | ⚠️ |
| cancelled | ❌ | ❌ | ⚠️ |

*Configured in competition settings

---

## 🔗 API ENDPOINTS

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/api/v1/admin/competitions` | List all |
| POST | `/api/v1/admin/competitions` | Create |
| GET | `/api/v1/admin/competitions/{id}` | View |
| PUT | `/api/v1/admin/competitions/{id}` | Update |
| DELETE | `/api/v1/admin/competitions/{id}` | Delete |

---

## 📋 REQUIRED FIELDS (Create/Update)

- ✅ `title` - Competition name
- ✅ `slug` - URL-friendly name
- ✅ `description` - Competition details
- ✅ `submission_deadline` - Date/time
- ✅ `status` - draft, published, etc.
- ⚡ `prizes` - Prize pool array
- ⚡ `judge_ids` - Judge user IDs
- ⚡ `sponsor_ids` - Sponsor IDs

---

## 🎨 CERTIFICATE FEATURES

| Feature | Status |
|---------|--------|
| A4 Landscape | ✅ |
| 300 DPI Print | ✅ |
| Color Preservation | ✅ |
| Full-Bleed | ✅ |
| Professional Design | ✅ |
| Auto-Generated | ✅ |
| Downloadable | ✅ |
| Browser Printable | ✅ |
| Profile Integration | ✅ |
| Unique ID | ✅ |

---

## 🚀 QUICK WORKFLOW

### Admin Creates Competition
```
1. POST /api/v1/admin/competitions (status: draft)
2. PUT /api/v1/admin/competitions/{id} (add images)
3. PUT /api/v1/admin/competitions/{id} (status: published)
4. Photographers submit
5. POST /api/v1/admin/competitions/{id}/calculate-winners
6. POST /api/v1/admin/competitions/{id}/announce-winners
7. POST /api/v1/admin/competitions/{id}/generate-certificates
8. Winners download certificates → Print on A4
```

---

## 🖨️ PRINT SETTINGS CHECKLIST

Before printing certificate:

- [ ] Paper Size: A4
- [ ] Orientation: Landscape
- [ ] Margins: None (0mm)
- [ ] Scale: 100% or Fit to Page
- [ ] Quality: Best/High
- [ ] Color: Full Color (not greyscale)
- [ ] Paper Type: Premium/Glossy
- [ ] DPI: 300+ (if available)

---

## 📞 SUPPORT

### Issues?

**Certificate not printing properly:**
- Check margins are set to "None"
- Verify scale is 100%
- Ensure colors are enabled
- Try "Print Background Graphics"

**Admin can't create competitions:**
- Verify user role is admin/super_admin/moderator
- Check API endpoint is correct
- Validate request body format

**Certificates not generating:**
- Winners must be announced first
- Check storage/certificates/ directory exists
- Verify file permissions

---

## 🎓 KEY POINTS

1. **Admin Control** ✅
   - Full CRUD for competitions
   - Role-based access
   - Data archival instead of deletion

2. **Professional Certificates** ✅
   - A4 landscape (297×210mm)
   - 300 DPI print quality
   - Color-coded by rank
   - Full-bleed design

3. **Easy Printing** ✅
   - Browser-based printing
   - One-click download
   - Print-ready PDF
   - Professional appearance

---

**Version:** 1.0  
**Date:** February 3, 2026  
**Status:** ✅ PRODUCTION READY
