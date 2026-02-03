# Quick Start: Sponsor & Contact Management

## 🚀 What's New?

Two complete admin panels have been added to manage sponsors and contact inquiries:

1. **Sponsors Management** → `/admin/sponsors`
2. **Contact Messages** → `/admin/contact-messages`

---

## 📍 Where to Find Them?

### From Admin Dashboard
1. Go to `/admin/dashboard`
2. Look for **Quick Actions** section at the bottom
3. Click the **⚡ Sponsors** icon or **✉️ Messages** icon

### Direct URLs
- **Sponsors**: `http://127.0.0.1:8000/admin/sponsors`
- **Messages**: `http://127.0.0.1:8000/admin/contact-messages`

---

## ⚙️ Setup Required

### Step 1: Run Migration
Create the `contact_messages` table:

```bash
cd c:\xampp\htdocs\Photographar SB
php artisan migrate
```

### Step 2: Clear Cache (Optional but Recommended)
```bash
php artisan cache:clear
php artisan config:clear
```

### Step 3: Test the Pages
1. Log in as admin
2. Navigate to `/admin/sponsors` or `/admin/contact-messages`
3. Test creating, editing, deleting records

---

## 📋 Sponsors Page (`/admin/sponsors`)

### What You Can Do:
- ✅ **View** all sponsors in a table
- ✅ **Create** new sponsor with form
- ✅ **Edit** existing sponsor
- ✅ **Delete** sponsor
- ✅ **Upload** sponsor logo
- ✅ **Search** sponsors by name
- ✅ **Filter** by status (Active/Inactive)
- ✅ **Set** display order
- ✅ **Add** website link
- ✅ **Set** date ranges

### Features:
- Logo preview in table
- Modal form for add/edit
- Confirmation before delete
- Real-time search
- Status toggle
- Date range picker

---

## 💬 Contact Messages Page (`/admin/contact-messages`)

### What You Can Do:
- ✅ **View** all inquiries and contact forms
- ✅ **Search** messages by name or email
- ✅ **Filter** by message type (Contact/Sponsorship)
- ✅ **Filter** by status (Pending/Read/Resolved)
- ✅ **View** full message details
- ✅ **Reply** to messages
- ✅ **Update** message status
- ✅ **Delete** messages
- ✅ **See** statistics

### Statistics Available:
- Total messages count
- Pending messages count
- Contact forms count
- Sponsorship inquiries count

### Message Types:
1. **Contact Form** - General inquiries
2. **Sponsorship** - Sponsorship requests

### Statuses:
1. **Pending** - New, unread message
2. **Read** - Message has been opened
3. **Resolved** - Message has been addressed

---

## 🔗 Connecting Sponsors & Contacts

### Workflow:
1. Admin receives sponsorship inquiry at `/admin/contact-messages`
2. Admin views the message details
3. If converting to sponsor, click **"Create Sponsor"** button
4. Creates new sponsor with inquiry info
5. Links the original inquiry to new sponsor

---

## 📊 API Endpoints

### Sponsors API
```
GET    /api/v1/admin/platform-sponsors        # List all
POST   /api/v1/admin/platform-sponsors        # Create
GET    /api/v1/admin/platform-sponsors/{id}   # Get one
PUT    /api/v1/admin/platform-sponsors/{id}   # Update
DELETE /api/v1/admin/platform-sponsors/{id}   # Delete
POST   /api/v1/admin/upload-logo              # Upload logo
```

### Contact Messages API
```
GET    /api/v1/admin/contact-messages         # List all
POST   /api/v1/admin/contact-messages         # Create
GET    /api/v1/admin/contact-messages/stats   # Get stats
GET    /api/v1/admin/contact-messages/{id}    # Get one
PATCH  /api/v1/admin/contact-messages/{id}    # Update status
DELETE /api/v1/admin/contact-messages/{id}    # Delete
```

---

## 🔐 Authentication

All features require:
- ✅ Login with admin account
- ✅ Valid Bearer token in localStorage
- ✅ Admin or super_admin role

---

## 💾 Database Tables

### contact_messages table
```
id                  - Primary key
name                - Sender name
email               - Sender email
phone               - Sender phone (optional)
subject             - Message subject
message             - Full message content
type                - 'contact' or 'sponsorship'
status              - 'pending', 'read', or 'resolved'
user_id             - Associated user (optional)
reply_count         - Number of replies sent
last_replied_at     - When last reply was sent
created_at          - Message timestamp
updated_at          - Last update timestamp
```

### sponsors table (already exists)
```
id                  - Primary key
name                - Sponsor name
slug                - URL-friendly name
logo                - Logo file path
website             - Website URL
description         - About sponsor
status              - 'active' or 'inactive'
display_order       - Display sequence
start_date          - Sponsorship start
end_date            - Sponsorship end
created_at          - Created timestamp
updated_at          - Updated timestamp
```

---

## 🧪 Testing the Features

### Test Sponsor CRUD:
1. ✅ Create sponsor with logo
2. ✅ View in table with preview
3. ✅ Edit sponsor details
4. ✅ Change status to inactive
5. ✅ Delete and confirm

### Test Contact Messages:
1. ✅ Submit contact form at `/contact`
2. ✅ See message in admin panel
3. ✅ Change status from pending to read
4. ✅ View full message details
5. ✅ Reply to message
6. ✅ Delete message

---

## 🐛 Troubleshooting

### Page Shows Blank/Error
- Check browser console for errors (F12)
- Verify admin login with valid token
- Check that migration ran: `php artisan migrate`

### Can't Upload Sponsor Logo
- Verify `storage/app/public` directory exists
- Check file permissions on `storage` folder
- Ensure file size < 5MB

### Messages Not Showing
- Verify contact form submits to `/api/v1/contact`
- Check `contact_messages` table has data
- Clear browser cache (Ctrl+F5)

### Routes Not Found
- Run `php artisan route:list` to verify routes
- Check Bearer token is valid
- Verify admin role is set on user

---

## 📱 Responsive Design

Both pages are fully responsive:
- ✅ Desktop (full layout)
- ✅ Tablet (adjusted grid)
- ✅ Mobile (single column)

---

## 🎯 Files Modified/Created

**New Files**:
- `resources/js/components/AdminSponsors.vue`
- `resources/js/components/AdminContactMessages.vue`
- `app/Models/ContactMessage.php`
- `app/Http/Controllers/Api/Admin/ContactMessageController.php`
- `database/migrations/2026_02_01_000000_create_contact_messages_table.php`

**Modified Files**:
- `resources/js/app.js` - Added routes and imports
- `routes/api.php` - Added API endpoints
- `resources/js/components/AdminDashboard.vue` - Added navigation links

---

## ✨ Key Features Highlight

### Admin Sponsors
- 🖼️ Logo upload with preview
- 🔍 Real-time search
- 📅 Date range picker
- 🎯 Display order management
- ✅ Status toggling
- 🗑️ Bulk delete support

### Admin Contact Messages
- 📨 Message categorization
- 🔍 Multi-field search
- 🏷️ Type & status filtering
- 📊 Statistics dashboard
- 📝 Reply system
- ✅ Status workflow

---

## 🔒 Security

- All endpoints require authentication
- Admin role verification
- Input validation on forms
- CSRF protection
- SQL injection prevention
- Proper error handling

---

## 📞 Support

For issues or questions:
1. Check the implementation guide: `SPONSOR_CONTACT_IMPLEMENTATION.md`
2. Review error messages in browser console
3. Check server logs: `storage/logs/laravel.log`

---

**Implementation Date**: February 1, 2025
**Status**: ✅ Complete and Ready to Use
