# POST-UPLOAD SETUP - Complete These Steps Now

## STEP 1: Extract Files (cPanel File Manager)

1. Go to: https://megna.bd.webxlogin.com:2083
2. Navigate to: `photographersb.com` folder
3. You should see your ZIP file
4. **Right-click the ZIP** → "Extract"
5. Extract to current directory: `/home/ichattogram/photographersb.com`
6. Click "Extract Files"
7. Wait for extraction (may take 2-3 minutes)
8. **Delete the ZIP file** after extraction

## STEP 2: Setup .env File

1. Find file: `.env.production`
2. **Right-click** → "Rename" → Change to: `.env`
3. **Right-click `.env`** → "Edit"
4. Find line: `DB_PASSWORD=YOUR_DATABASE_PASSWORD`
5. Replace `YOUR_DATABASE_PASSWORD` with your actual database password
6. Find line: `APP_KEY=base64:GENERATE_NEW_KEY_ON_SERVER`
7. We'll generate this in next step
8. Click "Save Changes"

## STEP 3: Generate Application Key

**Option A: Via Terminal (if available)**
1. In cPanel, search for "Terminal"
2. If found, click to open
3. Run these commands:
```bash
cd photographersb.com
php artisan key:generate
```

**Option B: Manual Generation (if no Terminal)**
Tell me if you don't have Terminal access, and I'll generate a key for you to paste.

## STEP 4: Set File Permissions

1. In File Manager, select `storage` folder
2. Right-click → "Change Permissions"
3. Set to: **755** (rwxr-xr-x)
4. ✅ Check "Recurse into subdirectories"
5. Click "Change Permissions"

6. Select `bootstrap/cache` folder
7. Right-click → "Change Permissions"
8. Set to: **755**
9. ✅ Check "Recurse into subdirectories"
10. Click "Change Permissions"

## STEP 5: Point Domain to Public Folder ⚠️ CRITICAL

Your domain MUST point to the `public` folder, not root.

1. In cPanel, search for **"Domains"**
2. Find `photographersb.com`
3. Click "Manage" or edit icon
4. Look for "Document Root" field
5. Change from: `/home/ichattogram/photographersb.com`
6. Change to: `/home/ichattogram/photographersb.com/public`
7. Save changes

## STEP 6: Import Database

1. In cPanel, open **"phpMyAdmin"**
2. Click on database: `ichattogram_photographersb` (left sidebar)
3. Click **"Import"** tab (top)
4. Click "Choose File"
5. Select: `database_export.sql` (I need to create this - tell me when ready)
6. Click "Go" at bottom
7. Wait for import to complete

## STEP 7: Run Migrations (if Terminal available)

```bash
cd photographersb.com
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## STEP 8: Test Your Site

Visit: https://photographersb.com

Expected result: Homepage should load!

---

## WHERE ARE YOU NOW?

Tell me which step you're on:
- [ ] Files extracted?
- [ ] .env renamed and edited?
- [ ] Do you have Terminal/SSH access?
- [ ] Need database SQL file?

Let me know and I'll help with the next step!
