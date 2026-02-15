# DEPLOYMENT INSTRUCTIONS - Photographer SB
# Server: ftp.ichattogram.com | Domain: photographersb.com

## PREPARATION COMPLETED ✅

### Database Info:
- Database: ichattogram_photographersb
- Username: ichattogram_photographersb
- Password: [You have this]

---

## STEP 2: Prepare Files for Upload

### Option A: Upload via cPanel File Manager (RECOMMENDED)

1. **Create ZIP file of your project:**
   - Go to: `C:\xampp\htdocs\Photographar SB`
   - Select these folders/files:
     ✅ app/
     ✅ bootstrap/
     ✅ config/
     ✅ database/
     ✅ public/
     ✅ resources/
     ✅ routes/
     ✅ storage/
     ✅ vendor/
     ✅ artisan
     ✅ composer.json
     ✅ composer.lock
     ✅ .env.production (rename to .env after upload)
     
   - ❌ EXCLUDE:
     - node_modules/
     - .git/
     - tests/
     - *.md files

2. **Upload via cPanel:**
   - Go to: https://megna.bd.webxlogin.com:2083
   - Click "File Manager"
   - Navigate to `public_html` (or your web root)
   - Click "Upload"
   - Upload the ZIP file
   - Right-click ZIP → Extract
   - Delete ZIP file after extraction

3. **Rename .env file:**
   - Find `.env.production`
   - Rename to `.env`

### Option B: Upload via FTP

1. **Use FTP Client (FileZilla/CoreFTP):**
   - Host: ftp.ichattogram.com
   - Port: 21
   - Username: admin@photographersb.com
   - Password: [Your FTP password]

2. **Upload to correct directory**

---

## NEXT: Tell me what folder you see in File Manager

Is it:
- [ ] public_html
- [ ] www  
- [ ] httpdocs
- [ ] Empty (root directory)
- [ ] Other: __________

Once confirmed, I'll create the final deployment script!
