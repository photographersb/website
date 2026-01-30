# COMPLETE DEPLOYMENT GUIDE - START TO FINISH
# Photographer SB Platform on photographersb.com

## PART 1: CLEAN UP (Fix the messed up extraction)

### Step 1: Delete Everything in photographersb.com
1. Go to cPanel File Manager
2. Navigate to: photographersb.com folder
3. Click "Select All"
4. Click "Delete"
5. Confirm deletion
6. The folder should now be EMPTY

### Step 2: Download Fresh ZIP from Local Computer
1. On your computer: C:\xampp\htdocs\Photographar SB\deployment-package\
2. Select ALL files/folders inside (app, bootstrap, config, etc.)
3. Right-click → Send to → Compressed (zipped) folder
4. Name it: photographer-sb.zip
5. You now have: C:\xampp\htdocs\Photographar SB\deployment-package\photographer-sb.zip

---

## PART 2: UPLOAD & EXTRACT CORRECTLY

### Step 3: Upload ZIP to Server
1. Go to cPanel File Manager
2. Make sure you're in photographersb.com folder (empty)
3. Click "Upload" button
4. Select your photographer-sb.zip file
5. Wait for upload (may take 5-10 minutes)

### Step 4: Extract ZIP File
1. You should see photographer-sb.zip in the folder
2. Right-click on it → "Extract"
3. Leave "Extract to current directory" selected
4. Click "Extract File(s)"
5. Wait for extraction to complete

### Step 5: Verify Structure
After extraction, you should see directly in photographersb.com:
✅ app/ (folder)
✅ bootstrap/ (folder)
✅ config/ (folder)
✅ database/ (folder)
✅ public/ (folder) ← THIS IS IMPORTANT
✅ resources/ (folder)
✅ routes/ (folder)
✅ storage/ (folder)
✅ vendor/ (folder)
✅ artisan (file)
✅ composer.json (file)
✅ composer.lock (file)
✅ package.json (file)

**NO NESTED DUPLICATES!** If you see:
❌ app/app/
❌ public/app/
This means wrong extraction - DELETE and try again!

### Step 6: Delete the ZIP File
1. Right-click photographer-sb.zip
2. Click "Delete"
3. Confirm

---

## PART 3: CREATE .ENV FILE

### Step 7: Create .env File
1. In photographersb.com folder (where app/, bootstrap/ etc are)
2. Click "+ File" button
3. Name it: .env (with the dot)
4. Click "Create New File"
5. Right-click .env → "Edit"
6. Copy ALL this text and paste:

```
APP_NAME="Photographer SB"
APP_ENV=production
APP_KEY=base64:6RNprv1oA33DGzNo7l8WGTdYOhgPs+JIUmTu+e84DSQ=
APP_DEBUG=false
APP_URL=https://photographersb.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ichattogram_photographersb
DB_USERNAME=ichattogram_photographersb
DB_PASSWORD=Nakib##123***

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mail.photographersb.com
MAIL_PORT=587
MAIL_USERNAME=noreply@photographersb.com
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@photographersb.com
MAIL_FROM_NAME="Photographer SB"

BKASH_APP_KEY=
BKASH_APP_SECRET=
BKASH_USERNAME=
BKASH_PASSWORD=
BKASH_BASE_URL=https://checkout.pay.bka.sh/v1.2.0-beta

SANCTUM_STATEFUL_DOMAINS=photographersb.com,www.photographersb.com
SESSION_DOMAIN=.photographersb.com
```

7. Click "Save Changes"

---

## PART 4: POINT DOMAIN TO PUBLIC FOLDER

### Step 8: Change Document Root in cPanel
1. Exit File Manager
2. Go to cPanel home
3. Find "Domains" section (look for addon domains icon)
4. Click "Domains"
5. You'll see list of domains including photographersb.com
6. Click the settings/manage icon next to photographersb.com
7. Look for "Document Root" field
8. It probably shows: /home/ichattogram/photographersb.com
9. Change it to: /home/ichattogram/photographersb.com/public
10. Click "Submit" or "Update"
11. Wait 1-2 minutes for changes to take effect

---

## PART 5: CHECK IF IT WORKS

### Step 9: Test Your Website
1. Open browser
2. Go to: https://photographersb.com/
3. Wait 2-3 seconds for page to load

**You should see:**
✅ Photographer SB homepage
✅ Navigation menu
✅ Login/Register buttons

**If you see:**
❌ Directory listing → Document root not changed correctly
❌ Error 500 → Database or permissions issue
❌ Blank page → Check storage/ folder permissions

---

## IF SOMETHING GOES WRONG

### Troubleshooting

**Problem: Still seeing directory listing**
- Document root change didn't take effect
- Try clearing browser cache (Ctrl+Shift+R)
- Wait another 5 minutes for DNS cache

**Problem: Error 500**
- Check if .env file exists and has correct database password
- Check storage/ and bootstrap/cache/ folder permissions (should be 755 or 775)

**Problem: Can't access database**
- Verify database credentials are correct
- Check if database ichattogram_photographersb exists in phpMyAdmin

---

## DONE? NEXT STEPS

Once website loads:
1. Check Admin dashboard: https://photographersb.com/admin
2. Check if you can login
3. Test API: https://photographersb.com/api/v1/competitions

If everything works, deployment is COMPLETE! 🎉

---

## READY? START WITH PART 1 STEP 1
