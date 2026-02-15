# UPLOAD PHOTOGRAPHER SB PROJECT - Step by Step

## Your Upload Directory: /home/ichattogram/photographersb.com

## STEP 1: Clean the Directory

1. Go to cPanel File Manager: https://megna.bd.webxlogin.com:2083
2. Navigate to `photographersb.com` folder
3. **Select All** files/folders (or just delete the demo Laravel)
4. Click "Delete"
5. Confirm deletion

## STEP 2: Prepare Your Project for Upload

**On your computer:**

1. Go to: `C:\xampp\htdocs\Photographar SB`
2. Create a ZIP file containing ONLY these folders/files:

**✅ INCLUDE:**
- app/
- bootstrap/
- config/
- database/
- public/
- resources/
- routes/
- storage/
- vendor/
- artisan
- composer.json
- composer.lock
- package.json

**❌ EXCLUDE (DO NOT ZIP):**
- node_modules/ (too large, not needed)
- .git/
- .env (you'll upload separately)
- *.md files (documentation)
- tests/

**To create ZIP:**
- Select all folders listed above
- Right-click → Send to → Compressed (zipped) folder
- Name it: `photographersb-project.zip`

## STEP 3: Upload to Server

1. In cPanel File Manager, open `photographersb.com` folder
2. Click **"Upload"** button (top toolbar)
3. Click "Select File" 
4. Choose your `photographersb-project.zip`
5. Wait for upload to complete (may take 5-10 minutes depending on size)

## STEP 4: Extract Files

1. After upload completes, you'll see `photographersb-project.zip` in the folder
2. Right-click the ZIP file
3. Click **"Extract"**
4. Extract to: `/home/ichattogram/photographersb.com`
5. Click "Extract File(s)"
6. After extraction, DELETE the ZIP file

## STEP 5: Upload .env File

1. In File Manager, still in `photographersb.com` folder
2. Click "+ File" button
3. Name it: `.env`
4. Right-click `.env` → "Edit"
5. Copy ALL content from: `C:\xampp\htdocs\Photographar SB\.env.production`
6. Paste into the editor
7. Find line: `DB_PASSWORD=YOUR_DATABASE_PASSWORD`
8. Replace with your actual database password
9. Click "Save Changes"

## STEP 6: Set Permissions

Select these folders:
- storage/
- bootstrap/cache/

Right-click → "Change Permissions"
Set to: 755 or 775

---

## Alternative: Upload via FTP Client

If ZIP upload fails (too large), use FTP:

1. Download FileZilla or CoreFTP
2. Connect:
   - Host: ftp.ichattogram.com
   - Port: 21
   - User: admin@photographersb.com
   - Pass: [your FTP password]
3. Navigate to: /photographersb.com/
4. Upload all folders/files directly (skip node_modules)

---

## READY TO START?

1. Delete demo Laravel files
2. Create ZIP of your project (excluding node_modules)
3. Upload & Extract
4. Upload .env with database password

Let me know when you've uploaded, and I'll give you the commands to complete setup!
