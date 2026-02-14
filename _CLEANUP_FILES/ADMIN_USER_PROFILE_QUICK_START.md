# Admin User Profile Management - Quick Reference

## What's New at `/admin/users`

### ✨ New Features
**Complete CRUD for Photographer Profile Data:**
- View all photographer profile information (bio, website, verification status)
- Edit photographer details without leaving the user list
- Delete photographer profiles when needed
- Real-time success/error feedback

### 📋 Profile Data Now Manageable
- ✏️ **Bio** - Professional biography
- 🌐 **Website URL** - Portfolio/personal website
- 🖼️ **Profile Image** - Profile photo URL
- ✅ **Verification Status** - Mark as verified/unverified

## How It Works

### Step 1: Open User List
Navigate to `http://127.0.0.1:8000/admin/users`

### Step 2: Find & View User
- Search for photographer by name/email
- Click "View" button to open profile

### Step 3: View Profile Data
In the modal, look for **"📷 Photographer Profile"** section showing:
- Verification status (✅ or ❌)
- Username/Slug
- Categories and cities
- Bio
- Website URL
- Profile image status

### Step 4: Edit or Delete
- Click **"✏️ Edit Profile Data"** to update any field
- Or click **"Delete Profile"** button to remove profile

### Step 5: Save Changes
- Click "Save Profile" to update
- Click "Delete Profile" to remove (with confirmation)
- Click "Cancel" to discard changes

## Modal Locations

### View Modal
**When:** Click "View" on any user
**Shows:** Basic user info + photographer profile section
**Action:** Click "✏️ Edit Profile Data"

### Edit Modal
**When:** Click "Edit Profile Data" button
**Edit:** Bio, Website URL, Profile Image, Verification Status
**Actions:** 
  - Save Profile (save changes)
  - Delete Profile (remove profile)
  - Cancel (discard changes)

## Status Indicators

✅ **Verified** - Photographer profile is verified
❌ **Not Verified** - Profile pending verification
📷 **Photographer Badge** - User has photographer profile

## API Calls (Behind the Scenes)

| Operation | Endpoint | Method | Purpose |
|-----------|----------|--------|---------|
| View User | `/api/v1/admin/users/{id}` | GET | Load full photographer data |
| Update Profile | `/api/v1/photographers/{id}` | PUT | Save profile changes |
| Delete Profile | `/api/v1/photographers/{id}` | DELETE | Remove photographer profile |

## Success Messages

- ✅ "Photographer profile updated successfully!" - Profile saved
- ✅ "Photographer profile deleted successfully!" - Profile removed
- ⚠️ "Error updating photographer profile" - Save failed
- ⚠️ "Error deleting photographer profile" - Delete failed

## Keyboard Shortcuts

| Action | Shortcut |
|--------|----------|
| Open Modal | Click "View" button |
| Close Modal | ESC key or click outside |
| Save Form | Click "Save Profile" |
| Cancel Form | Click "Cancel" button |

## Troubleshooting

### Profile Data Not Showing
- Ensure user has photographer role
- Check if user data has been fetched from API
- Refresh the page with F5

### Can't Edit Profile
- Check if you have admin privileges
- Try refreshing the page
- Check browser console for errors

### Delete Confirmation Not Working
- Ensure JavaScript is enabled
- Try refreshing the page
- Check if user's browser allows alerts

### API Errors
- Check network tab in DevTools
- Verify API endpoints are responding
- Check if auth token is valid

## Features Matrix

| Feature | Status | How To Use |
|---------|--------|-----------|
| View Profile | ✅ Active | Click View → See profile section |
| Edit Bio | ✅ Active | Edit Modal → Update textarea |
| Edit Website | ✅ Active | Edit Modal → Update URL |
| Edit Image URL | ✅ Active | Edit Modal → Update image |
| Toggle Verified | ✅ Active | Edit Modal → Check/Uncheck |
| Delete Profile | ✅ Active | Edit Modal → Delete button |
| Batch Edit | ⏳ Planned | Not yet available |
| Profile Templates | ⏳ Planned | Not yet available |
| Audit Trail | ⏳ Planned | Not yet available |

## Access Requirements

**Who Can Use This:**
- ✅ Super Admins
- ✅ Admins
- ✅ Moderators
- ❌ Regular Users
- ❌ Photographers (viewing their own only)

## Performance Notes

- Profile data loads when you click "View"
- Edits save immediately to database
- Deletes remove data permanently (use with care!)
- Toast notifications auto-dismiss after 3-5 seconds

## Related Pages

- 📊 [Admin Dashboard](/admin) - Main admin hub
- 👥 [Users Management](/admin/users) - This page
- 📸 [Photographers](/admin/photographers) - Photographer-specific management
- ⚙️ [Settings](/admin/settings) - System settings

## Last Updated
January 22, 2026

## Version
1.0.0

## Support
For issues or questions:
1. Check this guide
2. Review error messages in browser console
3. Check [ADMIN_USER_PROFILE_MANAGEMENT.md](ADMIN_USER_PROFILE_MANAGEMENT.md) for detailed docs
