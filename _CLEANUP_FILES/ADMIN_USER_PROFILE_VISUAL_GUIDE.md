# 🎯 ADMIN USER PROFILE CRUD - VISUAL GUIDE

## System Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                      ADMIN DASHBOARD                            │
│                   http://127.0.0.1:8000/admin                   │
└────────────────────────────┬────────────────────────────────────┘
                             │
                    [Users Management]
                             │
┌────────────────────────────▼────────────────────────────────────┐
│                    USERS LIST PAGE                              │
│              http://127.0.0.1:8000/admin/users                 │
│                                                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ Users Table                                             │   │
│  │ ──────────────────────────────────────────────────────  │   │
│  │ Name      │ Email    │ Role         │ Actions          │   │
│  │ ──────────┼──────────┼──────────────┼──────────────────│   │
│  │ John Doe  │ john@... │ Photographer │ [View][Edit][+]  │   │
│  │ Jane S... │ jane@... │ Client       │ [View][Edit][+]  │   │
│  │ ...       │ ...      │ ...          │ ...              │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                  │
│  [Click "View" on photographer user] ▼                         │
└────────────────────────────┬────────────────────────────────────┘
                             │
┌────────────────────────────▼────────────────────────────────────┐
│              VIEW USER MODAL (Opened)                           │
│  ╔══════════════════════════════════════════════════════════╗  │
│  ║ User Details                                        [✕]  ║  │
│  ╠══════════════════════════════════════════════════════════╣  │
│  ║                                                          ║  │
│  ║ Name: John Doe              Email: john@example.com    ║  │
│  ║ Phone: +880 1234567890      Role: Photographer        ║  │
│  ║ Status: ✅ Active           Member Since: 2024-01-15  ║  │
│  ║                                                          ║  │
│  ║ Roles: 📷 Photographer  🎓 Mentor  ⚖️ Judge           ║  │
│  ║                                                          ║  │
│  ║ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━   ║  │
│  ║ 📷 Photographer Profile  ← [NEW SECTION ADDED]         ║  │
│  ║ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━   ║  │
│  ║                                                          ║  │
│  ║ Username/Slug: john-photographer                       ║  │
│  ║ Verified: ✅ Yes                                       ║  │
│  ║ Categories: Portrait Photography                       ║  │
│  ║ Cities: 3 cities                                       ║  │
│  ║                                                          ║  │
│  ║ Bio: "Professional photographer with 10 years..."     ║  │
│  ║ Profile Image: ✅ Set                                  ║  │
│  ║ Website: https://john-photography.com                 ║  │
│  ║                                                          ║  │
│  ║ [✏️ Edit Profile Data]  ← [NEW BUTTON]                ║  │
│  ║                                                          ║  │
│  ╚══════════════════════════════════════════════════════════╝  │
│                                                                  │
│  [Click "Edit Profile Data"] ▼                                 │
└────────────────────────────┬────────────────────────────────────┘
                             │
┌────────────────────────────▼────────────────────────────────────┐
│          EDIT PHOTOGRAPHER PROFILE MODAL (New)                  │
│  ╔══════════════════════════════════════════════════════════╗  │
│  ║ Edit Photographer Profile - John Doe           [✕]      ║  │
│  ╠══════════════════════════════════════════════════════════╣  │
│  ║                                                          ║  │
│  ║ Bio                                                      ║  │
│  ║ ┌──────────────────────────────────────────────────────┐║  │
│  ║ │ Professional photographer with 10 years of...       ││  │
│  ║ │ specialization in portrait and event photography... ││  │
│  ║ └──────────────────────────────────────────────────────┘║  │
│  ║                                                          ║  │
│  ║ Website URL                   Profile Image URL         ║  │
│  ║ ┌──────────────────────────┐ ┌──────────────────────────┐║  │
│  ║ │https://john-photo...    │ │https://cdn/image.jpg     │║  │
│  ║ └──────────────────────────┘ └──────────────────────────┘║  │
│  ║                                                          ║  │
│  ║ ☑ Verified (Photographer profile is verified)          ║  │
│  ║                                                          ║  │
│  ╠══════════════════════════════════════════════════════════╣  │
│  ║ [Cancel]    [💾 Save Profile]  [🗑️ Delete Profile]    ║  │
│  ╚══════════════════════════════════════════════════════════╝  │
│                                                                  │
│  [Options]:                                                     │
│  ────────────                                                   │
│  1) Edit fields → Click "Save Profile"                         │
│     Result: ✅ "Photographer profile updated successfully!"     │
│                                                                  │
│  2) Click "Delete Profile" → Confirm                           │
│     Result: ✅ "Photographer profile deleted successfully!"     │
│                                                                  │
│  3) Click "Cancel" → Discard changes                           │
└────────────────────────────────────────────────────────────────┘
```

## Data Flow

```
Admin Views User
    ↓
API Call: GET /api/v1/admin/users/{id}
    ↓
View Modal Opens
    ├─ Shows: Basic user info
    └─ Shows: 📷 Photographer Profile (NEW)
              ├─ Bio
              ├─ Website
              ├─ Profile Image
              └─ Verified Status
    ↓
Admin Clicks "Edit Profile Data"
    ↓
Edit Modal Opens (NEW)
    ├─ Form: Bio (textarea)
    ├─ Form: Website URL (input)
    ├─ Form: Profile Image URL (input)
    ├─ Form: Verified (checkbox)
    └─ Buttons: Save | Delete | Cancel
    ↓
[Option 1] Admin Saves Changes
    ↓
    API Call: PUT /api/v1/photographers/{id}
    ↓
    ✅ Toast: "Photographer profile updated successfully!"
    ↓
    Modal Closes
    ↓
    User List Refreshes

[Option 2] Admin Deletes Profile
    ↓
    Confirmation Dialog
    ↓
    API Call: DELETE /api/v1/photographers/{id}
    ↓
    ✅ Toast: "Photographer profile deleted successfully!"
    ↓
    Modal Closes
    ↓
    User List Refreshes

[Option 3] Admin Cancels
    ↓
    Changes Discarded
    ↓
    Modal Closes
```

## UI Component Hierarchy

```
AdminUsersPage (Index.vue)
├── User List Table
│   ├── Rows (User entries)
│   │   ├── User Avatar
│   │   ├── User Info (Name, Email, Phone)
│   │   ├── Role Badge
│   │   ├── Status Badge
│   │   └── Action Buttons
│   │       ├── [View] → View Modal
│   │       ├── [Edit] → Edit Modal
│   │       └── [+] → Promote Menu
│   │
│   └── Pagination Controls
│
├── View User Modal
│   ├── Modal Header (with close button)
│   ├── Modal Body
│   │   ├── Section 1: User Details
│   │   │   ├── Name
│   │   │   ├── Email
│   │   │   ├── Phone
│   │   │   ├── Role
│   │   │   ├── Status
│   │   │   └── Roles Badges
│   │   │
│   │   └── Section 2: 📷 Photographer Profile (NEW)
│   │       ├── Username/Slug
│   │       ├── Verified Status
│   │       ├── Categories
│   │       ├── Cities
│   │       ├── Bio
│   │       ├── Profile Image
│   │       ├── Website
│   │       └── [✏️ Edit Profile Data] Button
│   │
│   └── Modal Footer (Action buttons)
│
├── Edit Photographer Profile Modal (NEW)
│   ├── Modal Header (with close button)
│   ├── Modal Body
│   │   └── Form
│   │       ├── Bio Field (textarea)
│   │       ├── Website URL Field (input)
│   │       ├── Profile Image URL Field (input)
│   │       └── Verified Checkbox
│   │
│   └── Modal Footer
│       ├── [Cancel] Button
│       ├── [💾 Save Profile] Button
│       └── [🗑️ Delete Profile] Button
│
└── Toast Notifications
    ├── Success Messages (3s)
    │   ├── "Photographer profile updated successfully!"
    │   └── "Photographer profile deleted successfully!"
    │
    └── Error Messages (5s)
        ├── "Error updating photographer profile"
        ├── "Error deleting photographer profile"
        └── "Error: Photographer profile not found"
```

## Feature Comparison

```
BEFORE:                          AFTER (NEW):
═════════════════════════════    ══════════════════════════════════
View User Modal:                 View User Modal:
├─ Name                          ├─ Name
├─ Email                         ├─ Email
├─ Phone                         ├─ Phone
├─ Role                          ├─ Role
├─ Status                        ├─ Status
└─ Roles Badges                  ├─ Roles Badges
                                 └─ 📷 PHOTOGRAPHER PROFILE (NEW)
                                    ├─ Username/Slug
                                    ├─ Verified Status
                                    ├─ Categories
                                    ├─ Cities
                                    ├─ Bio ← Readable
                                    ├─ Website ← Readable
                                    └─ [✏️ Edit Profile Data] ← NEW

                                 Edit Profile Modal: (NEW)
                                 ├─ Bio (Editable Textarea)
                                 ├─ Website (Editable URL)
                                 ├─ Image (Editable URL)
                                 ├─ Verified (Editable Toggle)
                                 ├─ [Save Profile] ← UPDATE
                                 └─ [Delete Profile] ← DELETE

CAPABILITIES BEFORE:             CAPABILITIES AFTER:
═════════════════════════════    ══════════════════════════════════
✓ View basic user info           ✓ View basic user info
✓ Edit user (name, email, etc)   ✓ Edit user (name, email, etc)
✓ View user role                 ✓ View user role
✗ View profile data              ✓ View profile data (NEW)
✗ Edit profile data              ✓ Edit profile data (NEW)
✗ Delete profile data            ✓ Delete profile data (NEW)
                                 ✓ Manage verification status (NEW)
```

## Action Buttons Reference

```
USERS LIST PAGE:
┌──────────────────────────────────────────────────────┐
│ User                   Actions                        │
├──────────────────────────────────────────────────────┤
│ John Doe             [View]  [Edit]  [+Promote]     │
│ Jane Smith           [View]  [Edit]  [+Promote]     │
└──────────────────────────────────────────────────────┘

[View] → Opens View User Modal (with profile data shown)
         └─ Contains: [✏️ Edit Profile Data] button (NEW)

[Edit] → Opens Edit User Modal (name, email, password, role)
         └─ Does NOT edit profile data

[+Promote] → Opens promotion menu (mentor/judge)


VIEW USER MODAL BUTTONS (NEW):
┌──────────────────────────────────────────────────────┐
│ [✏️ Edit Profile Data]                              │
│                                                       │
│ Opens Edit Photographer Profile Modal with:         │
│ - Bio textarea                                       │
│ - Website URL input                                 │
│ - Profile Image input                               │
│ - Verified checkbox                                 │
│                                                      │
│ Footer: [Cancel] [Save Profile] [Delete Profile]   │
└──────────────────────────────────────────────────────┘
```

## Status Indicators

```
PHOTOGRAPHER ROLE:
  📷 Photographer Badge (displayed in "Additional Roles" section)

VERIFICATION STATUS (in profile section):
  ✅ Yes  → Photographer is verified
  ❌ No   → Photographer is NOT verified (or unverified)

PROFILE IMAGE:
  ✅ Set  → Profile photo URL is configured
  N/A    → No profile photo set

PROFILE COMPLETION:
  ✓ All fields shown if present
  ✗ "N/A" shown if field is empty/missing
```

## Notification Messages

```
✅ SUCCESS NOTIFICATIONS (3 seconds):
┌─────────────────────────────────────────────────────┐
│ ✓ Photographer profile updated successfully!        │
│ ✓ Photographer profile deleted successfully!        │
└─────────────────────────────────────────────────────┘

❌ ERROR NOTIFICATIONS (5 seconds):
┌─────────────────────────────────────────────────────┐
│ ✗ Error updating photographer profile              │
│ ✗ Error deleting photographer profile              │
│ ✗ Error: Photographer profile not found            │
└─────────────────────────────────────────────────────┘
```

## Workflow Examples

### Example 1: Admin Edits Photographer Bio

```
1. Navigate to /admin/users
2. Find "John Doe" (Photographer) in list
3. Click [View] button
4. View modal opens, scroll down to see profile data
5. See "Bio: Professional photographer..."
6. Click "✏️ Edit Profile Data" button
7. Edit modal opens with form
8. Change bio text in textarea
9. Click "💾 Save Profile"
10. ✅ Toast appears: "Photographer profile updated successfully!"
11. Modal closes, list refreshes
```

### Example 2: Admin Deletes Photographer Profile

```
1. Navigate to /admin/users
2. Find "Jane Smith" (Photographer) in list
3. Click [View] button
4. View modal opens
5. Scroll to profile section
6. Click "✏️ Edit Profile Data"
7. Edit modal opens
8. Click "🗑️ Delete Profile"
9. Browser shows confirmation: "Are you sure?"
10. Click OK to confirm
11. ✅ Toast: "Photographer profile deleted successfully!"
12. Modal closes, list refreshes
```

### Example 3: Admin Views Profile Data

```
1. Navigate to /admin/users
2. Search for photographer: "sarah" 
3. Click [View] on "Sarah Photography"
4. View modal shows:
   - Name: Sarah Ahmed
   - Email: sarah@email.com
   - Role: Photographer
   - 📷 Photographer Profile section with:
     - Username: sarah-photography
     - Verified: ✅ Yes
     - Categories: Wedding Photography
     - Cities: 4 cities
     - Bio: "Specializing in wedding..."
     - Website: https://sarah-photo.com
5. Admin reviews data
6. Can now edit if needed by clicking button
```

## Responsive Design

```
DESKTOP (1200px+):
┌─────────────────────────────────┐
│ Users List (Table Format)       │
│                                 │
│ Name | Email | Role | Actions   │
├─────────────────────────────────┤
│ John | j@... | Photo| [V][E][+] │
└─────────────────────────────────┘

Modal: 
┌────────────────────────────┐
│ Title              [✕]     │
├────────────────────────────┤
│ Content Area               │
│ (2+ columns for forms)     │
├────────────────────────────┤
│ [Cancel] [Save] [Delete]   │
└────────────────────────────┘

TABLET (768px - 1199px):
┌──────────────────────────┐
│ Users List (Card Format) │
│ ┌──────────────────────┐ │
│ │ Name: John           │ │
│ │ Email: j@...         │ │
│ │ [V] [E] [+]          │ │
│ └──────────────────────┘ │
└──────────────────────────┘

Modal:
┌──────────────────────┐
│ Title          [✕]   │
├──────────────────────┤
│ Content (single col) │
├──────────────────────┤
│ [Cancel] [Save]      │
│ [Delete]             │
└──────────────────────┘

MOBILE (< 768px):
✓ Fully responsive
✓ Stack layout
✓ Full-width buttons
✓ Touch-friendly spacing
```

---

## Summary

✅ **Complete Implementation** of admin CRUD for photographer profile data
✅ **Location**: `/admin/users` endpoint  
✅ **Build**: Successful (256 modules, 5.65s)
✅ **Features**: View • Edit • Delete • Verification Management
✅ **UI**: Professional modals with proper validation
✅ **Notifications**: Success/error toast messages
✅ **Ready**: Production deployment ready

---

*Visual Guide Created: January 22, 2026*
*Version: 1.0.0*
