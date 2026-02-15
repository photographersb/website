# Architecture & System Diagram

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         PHOTOGRAPHAR ADMIN PANEL                         │
│                                                                           │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    ADMIN DASHBOARD (/admin/dashboard)            │   │
│  │                                                                   │   │
│  │  ┌─────────────────────────────────────────────────────────┐   │   │
│  │  │          QUICK ACTIONS SECTION                          │   │   │
│  │  │                                                         │   │   │
│  │  │  [Users]  [Photographers]  [Bookings]  [Comps]  [Reviews]  │   │
│  │  │  [Transactions]  [⚡ SPONSORS]  [✉️ MESSAGES]          │   │   │
│  │  │                                                         │   │   │
│  │  └─────────────────────────────────────────────────────────┘   │   │
│  │                            ↓                                     │   │
│  │              ┌──────────────────────────┐                       │   │
│  │              ↓                          ↓                       │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                           │
│     SPONSORS PAGE                              MESSAGES PAGE             │
│   (/admin/sponsors)                    (/admin/contact-messages)        │
│   ┌──────────────────────────┐         ┌──────────────────────────┐     │
│   │ AdminSponsors.vue        │         │ AdminContactMessages.vue │     │
│   │                          │         │                          │     │
│   │ • List View              │         │ • Message List           │     │
│   │ • Search/Filter          │         │ • Multi-Filter           │     │
│   │ • Create Modal           │         │ • Detail Modal           │     │
│   │ • Edit Modal             │         │ • Reply System           │     │
│   │ • Delete Confirm         │         │ • Status Workflow        │     │
│   │ • Logo Upload            │         │ • Statistics             │     │
│   └──────────────────────────┘         └──────────────────────────┘     │
│                ↓                                  ↓                       │
└─────────────────────────────────────────────────────────────────────────┘
                  ↓                                  ↓
        ┌─────────────────────┐         ┌─────────────────────┐
        │   SPONSORS API      │         │  MESSAGES API       │
        ├─────────────────────┤         ├─────────────────────┤
        │ GET    /platform... │         │ GET /contact-msgs   │
        │ POST   /platform... │         │ POST /contact-msgs  │
        │ PUT    /platform... │         │ PATCH /contact-msgs │
        │ DELETE /platform... │         │ DELETE /contact-msgs│
        │ POST   /upload-logo │         │ GET /stats          │
        └─────────────────────┘         └─────────────────────┘
                  ↓                                  ↓
        ┌─────────────────────┐         ┌─────────────────────┐
        │ SponsorController   │         │ ContactMessage      │
        │                     │         │ Controller          │
        │ • index()           │         │                     │
        │ • store()           │         │ • index()           │
        │ • update()          │         │ • show()            │
        │ • destroy()         │         │ • store()           │
        │ • uploadLogo()      │         │ • update()          │
        └─────────────────────┘         │ • updateStatus()    │
                  ↓                     │ • destroy()         │
        ┌─────────────────────┐         │ • stats()           │
        │  Sponsor Model      │         └─────────────────────┘
        │                     │                    ↓
        │ • name              │         ┌─────────────────────┐
        │ • slug              │         │ContactMessage Model │
        │ • logo              │         │                     │
        │ • website           │         │ • name              │
        │ • description       │         │ • email             │
        │ • status            │         │ • type              │
        │ • display_order     │         │ • status            │
        │ • dates             │         │ • message           │
        └─────────────────────┘         │ • user_id           │
                  ↓                     │ • reply_count       │
        ┌─────────────────────┐         └─────────────────────┘
        │  SPONSORS TABLE     │                    ↓
        │                     │         ┌─────────────────────┐
        │ ✓ id                │         │ CONTACT_MESSAGES    │
        │ ✓ name              │         │ TABLE               │
        │ ✓ slug              │         │                     │
        │ ✓ logo              │         │ ✓ id                │
        │ ✓ website           │         │ ✓ name              │
        │ ✓ description       │         │ ✓ email             │
        │ ✓ status            │         │ ✓ phone             │
        │ ✓ display_order     │         │ ✓ subject           │
        │ ✓ start_date        │         │ ✓ message           │
        │ ✓ end_date          │         │ ✓ type              │
        │ ✓ created_at        │         │ ✓ status            │
        │ ✓ updated_at        │         │ ✓ user_id           │
        └─────────────────────┘         │ ✓ reply_count       │
                                        │ ✓ last_replied_at   │
                                        │ ✓ created_at        │
                                        │ ✓ updated_at        │
                                        └─────────────────────┘
```

---

## Data Flow Diagram

### Sponsors Management Flow

```
START
  ↓
ADMIN DASHBOARD
  ↓
Click "Sponsors"
  ↓
/admin/sponsors
  ↓
AdminSponsors.vue loads
  ↓
fetchSponsors()
  ↓
GET /api/v1/admin/platform-sponsors
  ↓
SponsorController::index()
  ↓
Sponsors Table ← Query results
  ↓
Display in Component
  ↓
USER ACTIONS:
  ├─ SEARCH
  │   ├─ Enter text in search box
  │   ├─ Triggers computed property
  │   └─ Filters local data
  │
  ├─ FILTER BY STATUS
  │   ├─ Select Active/Inactive
  │   ├─ Triggers computed property
  │   └─ Filters local data
  │
  ├─ CREATE SPONSOR
  │   ├─ Click "Add Sponsor"
  │   ├─ Modal form opens
  │   ├─ Enter sponsor details + logo
  │   ├─ POST /api/v1/admin/platform-sponsors
  │   ├─ SponsorController::store()
  │   ├─ Save to database
  │   ├─ Return new sponsor
  │   └─ Add to table (local)
  │
  ├─ EDIT SPONSOR
  │   ├─ Click sponsor row
  │   ├─ Modal form opens with data
  │   ├─ Modify details
  │   ├─ PUT /api/v1/admin/platform-sponsors/{id}
  │   ├─ SponsorController::update()
  │   ├─ Update database
  │   ├─ Return updated sponsor
  │   └─ Update in table (local)
  │
  ├─ UPLOAD LOGO
  │   ├─ Select file in form
  │   ├─ Image preview shown
  │   ├─ POST /api/v1/admin/upload-logo (multipart)
  │   ├─ Save file to storage
  │   └─ Return file path
  │
  └─ DELETE SPONSOR
      ├─ Click delete icon
      ├─ Confirmation dialog
      ├─ DELETE /api/v1/admin/platform-sponsors/{id}
      ├─ SponsorController::destroy()
      ├─ Delete from database
      ├─ Delete file from storage
      └─ Remove from table (local)
END
```

### Contact Messages Management Flow

```
START
  ↓
CONTACT FORM SUBMISSION (Public)
  ├─ User fills form
  ├─ POST /api/v1/contact
  ├─ Store in contact_messages table
  └─ type = 'contact', status = 'pending'
  ↓
ADMIN DASHBOARD
  ↓
Click "Messages"
  ↓
/admin/contact-messages
  ↓
AdminContactMessages.vue loads
  ↓
fetchMessages()
  ↓
GET /api/v1/admin/contact-messages
  ↓
ContactMessageController::index()
  ↓
Contact Messages Table ← Query results + stats
  ↓
Display in Component with stats
  ↓
USER ACTIONS:
  ├─ SEARCH
  │   ├─ Enter name/email in search
  │   ├─ Triggers computed property
  │   └─ Filters local data in real-time
  │
  ├─ FILTER BY TYPE
  │   ├─ Select Contact or Sponsorship
  │   ├─ Triggers computed property
  │   └─ Shows only matching type
  │
  ├─ FILTER BY STATUS
  │   ├─ Select Pending, Read, or Resolved
  │   ├─ Triggers computed property
  │   └─ Shows only matching status
  │
  ├─ VIEW MESSAGE
  │   ├─ Click "View" button
  │   ├─ GET /api/v1/admin/contact-messages/{id}
  │   ├─ ContactMessageController::show()
  │   ├─ If status = pending: mark as read
  │   ├─ Display full message in modal
  │   └─ Show reply button
  │
  ├─ UPDATE STATUS
  │   ├─ Click status dropdown
  │   ├─ Select new status
  │   ├─ PATCH /api/v1/admin/contact-messages/{id}
  │   ├─ ContactMessageController::updateStatus()
  │   ├─ Update database
  │   └─ Update local data
  │
  ├─ REPLY TO MESSAGE
  │   ├─ Click "Reply" button
  │   ├─ Reply modal opens
  │   ├─ Enter reply message
  │   ├─ POST /api/v1/admin/send-reply
  │   ├─ Send email to sender
  │   ├─ Update status to 'resolved'
  │   └─ Increment reply_count
  │
  └─ DELETE MESSAGE
      ├─ Click delete button
      ├─ Confirmation dialog
      ├─ DELETE /api/v1/admin/contact-messages/{id}
      ├─ ContactMessageController::destroy()
      ├─ Delete from database
      └─ Remove from table (local)
END
```

---

## Component Tree

```
App.vue (Root)
  ├─ Router
  │   ├─ AdminDashboard.vue
  │   │   ├─ Stats Cards
  │   │   ├─ Charts & Graphs
  │   │   └─ Quick Actions
  │   │       ├─ RouterLink → AdminSponsors
  │   │       └─ RouterLink → AdminContactMessages
  │   │
  │   ├─ AdminSponsors.vue (NEW)
  │   │   ├─ Header
  │   │   ├─ Filters
  │   │   ├─ Search Box
  │   │   ├─ Stats Cards
  │   │   ├─ Sponsors Table
  │   │   │   ├─ Logo Preview (column)
  │   │   │   ├─ Name (column)
  │   │   │   ├─ Website (column)
  │   │   │   ├─ Status (column)
  │   │   │   ├─ Display Order (column)
  │   │   │   ├─ Date Range (column)
  │   │   │   ├─ Actions (column)
  │   │   │   │   ├─ Edit Button
  │   │   │   │   └─ Delete Button
  │   │   │   └─ Rows (sponsors)
  │   │   └─ CreateEditModal
  │   │       ├─ Form Fields
  │   │       ├─ Logo Upload
  │   │       ├─ Date Picker
  │   │       ├─ Status Dropdown
  │   │       └─ Submit Button
  │   │
  │   └─ AdminContactMessages.vue (NEW)
  │       ├─ Header
  │       ├─ Filters
  │       │   ├─ Search Box
  │       │   ├─ Type Filter
  │       │   └─ Status Filter
  │       ├─ Stats Cards
  │       │   ├─ Total Messages
  │       │   ├─ Pending
  │       │   ├─ Contact Forms
  │       │   └─ Sponsorship Inquiries
  │       ├─ Messages Table
  │       │   ├─ From Name (column)
  │       │   ├─ Email (column)
  │       │   ├─ Type Badge (column)
  │       │   ├─ Subject (column)
  │       │   ├─ Status Dropdown (column)
  │       │   ├─ Date (column)
  │       │   ├─ Actions (column)
  │       │   │   ├─ View Button
  │       │   │   └─ Delete Button
  │       │   └─ Rows (messages)
  │       ├─ DetailModal
  │       │   ├─ Sender Info
  │       │   ├─ Message Content
  │       │   ├─ Status Indicator
  │       │   ├─ Reply Button
  │       │   ├─ Create Sponsor Button (if sponsorship)
  │       │   └─ Close Button
  │       └─ ReplyModal
  │           ├─ To Email (display)
  │           ├─ Subject Field
  │           ├─ Message Textarea
  │           ├─ Send Button
  │           └─ Cancel Button
  │
  └─ ... (Other routes)
```

---

## Database Schema Diagram

```
CONTACT_MESSAGES TABLE
┌────────────────────────────────────────────────────────────────┐
│                                                                │
│  PK │ id              │ BIGINT AUTO_INCREMENT PRIMARY KEY      │
├─────┼─────────────────────────────────────────────────────────┤
│     │ name            │ VARCHAR(255) NOT NULL                  │
│     │ email           │ VARCHAR(255) NOT NULL ◄─── INDEX       │
│     │ phone           │ VARCHAR(20) NULLABLE                   │
│     │ subject         │ VARCHAR(255) NOT NULL                  │
│     │ message         │ LONGTEXT NOT NULL                      │
│     │ type            │ ENUM('contact','sponsorship') ◄─ INDEX│
│     │ status          │ ENUM('pending','read','resolved') ◄─ I│
│     │ user_id         │ BIGINT NULLABLE FK → users(id)         │
│     │ reply_count     │ INT DEFAULT 0                          │
│     │ last_replied_at │ TIMESTAMP NULLABLE                     │
│     │ created_at      │ TIMESTAMP ◄─── INDEX                  │
│     │ updated_at      │ TIMESTAMP                              │
│                                                                │
└────────────────────────────────────────────────────────────────┘

SPONSORS TABLE (Existing)
┌────────────────────────────────────────────────────────────────┐
│                                                                │
│  PK │ id              │ BIGINT AUTO_INCREMENT PRIMARY KEY      │
├─────┼─────────────────────────────────────────────────────────┤
│     │ name            │ VARCHAR(255) NOT NULL                  │
│     │ slug            │ VARCHAR(255) UNIQUE NOT NULL           │
│     │ logo            │ VARCHAR(255) NULLABLE                  │
│     │ website         │ VARCHAR(255) NULLABLE                  │
│     │ description     │ LONGTEXT NULLABLE                      │
│     │ status          │ ENUM('active','inactive') DEFAULT 'ac' │
│     │ display_order   │ INT DEFAULT 0                          │
│     │ start_date      │ DATE NULLABLE                          │
│     │ end_date        │ DATE NULLABLE                          │
│     │ created_at      │ TIMESTAMP                              │
│     │ updated_at      │ TIMESTAMP                              │
│                                                                │
└────────────────────────────────────────────────────────────────┘
```

---

## State Management Diagram

```
AdminSponsors.vue Component State

┌─────────────────────────────────────────────────────────────┐
│                     REACTIVE STATE                          │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  sponsors: []              ◄─ Array of all sponsors        │
│  loading: false            ◄─ API call in progress         │
│  error: ''                 ◄─ Error message                │
│  searchQuery: ''           ◄─ Search input value           │
│  filterStatus: ''          ◄─ Status filter value          │
│  selectedSponsor: null     ◄─ For edit modal               │
│  showModal: false          ◄─ Modal open/close state       │
│  modalMode: 'create'       ◄─ 'create' or 'edit'           │
│  uploadingLogo: false      ◄─ Logo upload in progress      │
│                                                             │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                   COMPUTED PROPERTIES                        │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  filteredSponsors = sponsors.filter(...)                    │
│    ├─ by searchQuery (name contains)                       │
│    ├─ by filterStatus (exact match)                        │
│    └─ returns filtered array                               │
│                                                             │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                    METHODS (Actions)                         │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  fetchSponsors()      ◄─ GET from API, populate state     │
│  handleLogoUpload()   ◄─ Upload file to server            │
│  saveSponsor()        ◄─ POST/PUT to API, update state    │
│  editSponsor()        ◄─ Open modal with data             │
│  deleteSponsor()      ◄─ DELETE from API, update state    │
│  closeModal()         ◄─ Close modal and reset            │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## API Request/Response Cycle

```
BROWSER (Vue Component)
    ↓
    └─ GET /api/v1/admin/contact-messages
       (Authorization: Bearer TOKEN)
            ↓
            └─ ROUTER (routes/api.php)
               ├─ Middleware: auth:sanctum (verify token)
               ├─ Middleware: admin role (verify admin)
               └─ Route → ContactMessageController@index
                    ↓
                    └─ ContactMessageController::index()
                       ├─ Query database
                       ├─ Apply filters (if any)
                       ├─ Return JSON response
                       └─ [200 OK] {data: [...], stats: {...}}
                            ↓
                            └─ Browser receives response
                               ├─ Parse JSON
                               ├─ Update Vue state
                               ├─ Render template
                               └─ Display to user
```

---

## Error Handling Flow

```
USER ACTION
    ↓
API CALL MADE
    ↓
    ├─ SUCCESS (200)
    │   ├─ Parse response
    │   ├─ Update component state
    │   └─ Display success (implicit)
    │
    └─ ERROR
        ├─ Network Error (no response)
        │   ├─ Catch in try-catch
        │   ├─ Set error message
        │   └─ Show error banner
        │
        ├─ Auth Error (401)
        │   ├─ Invalid or expired token
        │   ├─ Redirect to login
        │   └─ Show "Session expired"
        │
        ├─ Permissions Error (403)
        │   ├─ User not admin
        │   ├─ Redirect to home
        │   └─ Show "Access denied"
        │
        ├─ Validation Error (422)
        │   ├─ Invalid form data
        │   ├─ Show field errors
        │   └─ Keep modal open
        │
        ├─ Not Found Error (404)
        │   ├─ Resource deleted by someone else
        │   ├─ Refresh list
        │   └─ Show "Not found" message
        │
        └─ Server Error (500)
            ├─ Unexpected error
            ├─ Log to console
            └─ Show "Server error, try again"
```

---

## File Organization

```
Laravel Project Root
│
├─ app/
│   ├─ Http/
│   │   └─ Controllers/
│   │       └─ Api/
│   │           ├─ Admin/
│   │           │   ├─ SponsorController.php ✓ (existing)
│   │           │   └─ ContactMessageController.php ✓ (NEW)
│   │           └─ ... (other controllers)
│   │
│   └─ Models/
│       ├─ Sponsor.php ✓ (existing)
│       ├─ ContactMessage.php ✓ (NEW)
│       └─ ... (other models)
│
├─ database/
│   ├─ migrations/
│   │   ├─ 2026_01_29_184727_create_sponsors_table.php ✓ (existing)
│   │   └─ 2026_02_01_000000_create_contact_messages_table.php ✓ (NEW)
│   └─ ...
│
├─ routes/
│   └─ api.php ✓ (MODIFIED - added contact endpoints)
│
├─ resources/
│   └─ js/
│       ├─ components/
│       │   ├─ AdminDashboard.vue ✓ (MODIFIED - added links)
│       │   ├─ AdminSponsors.vue ✓ (NEW)
│       │   ├─ AdminContactMessages.vue ✓ (NEW)
│       │   └─ ... (other components)
│       │
│       ├─ app.js ✓ (MODIFIED - added routes & imports)
│       └─ bootstrap.js
│
└─ storage/
    └─ app/
        └─ public/
            └─ logos/ (sponsor logos stored here)
```

---

## Deployment Architecture

```
┌─────────────────────────────────────┐
│      LOCAL DEVELOPMENT              │
│  (xampp/htdocs/Photographar SB)     │
│                                     │
│  ├─ Vue Components                  │
│  ├─ Laravel Backend                 │
│  ├─ SQLite/MySQL Database           │
│  └─ Storage/Logs                    │
│                                     │
└────────────┬────────────────────────┘
             │ Deploy
             ↓
┌─────────────────────────────────────┐
│        PRODUCTION SERVER            │
│      (Web Host / cPanel)            │
│                                     │
│  ├─ Vue Components (built)          │
│  ├─ Laravel Backend (optimized)     │
│  ├─ MySQL Database                  │
│  ├─ Storage (for logos)             │
│  └─ Logs (monitored)                │
│                                     │
└─────────────────────────────────────┘
```

---

## Summary

✅ **Complete system architecture for sponsor and contact management**
✅ **All components integrated into existing admin panel**
✅ **Database schema with proper relationships**
✅ **RESTful API design with validation**
✅ **Responsive Vue components with state management**
✅ **Error handling and user feedback**
✅ **Ready for production deployment**

---

*Diagram created: February 1, 2025*
