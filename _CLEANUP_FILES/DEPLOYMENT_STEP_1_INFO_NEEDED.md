# DEPLOYMENT GUIDE - Photographer SB Platform
# Server: ftp.ichattogram.com
# Domain: photographersb.com

## ✅ STEP 1 COMPLETED: Production .env Created
File created: .env.production

**BEFORE UPLOADING**, you need to provide:

### Required Information:

1. **Database Credentials** (from your hosting control panel):
   - Database Name: ________________
   - Database Username: ________________
   - Database Password: ________________
   - Database Host: (usually 'localhost') ________________

2. **FTP Upload Directory**:
   - Where should files go? ________________
   - Common options: /public_html, /www, /httpdocs, or custom folder

3. **Email Configuration** (for notifications):
   - SMTP Host: ________________
   - SMTP Username: ________________
   - SMTP Password: ________________
   - SMTP Port: (usually 587 or 465) ________________

4. **Payment Gateway** (bKash credentials):
   - App Key: ________________
   - App Secret: ________________
   - Username: ________________
   - Password: ________________

5. **Server Capabilities**:
   - [ ] Do you have cPanel access?
   - [ ] Can you access PHP version selector?
   - [ ] Can you run commands via SSH/Terminal?
   - [ ] Is Composer installed on server?

---

## NEXT STEPS:

Once you provide the above information, I will:
- **Step 2**: Update .env.production with your actual credentials
- **Step 3**: Create database import script
- **Step 4**: Prepare files for upload (exclude unnecessary files)
- **Step 5**: Create FTP upload instructions
- **Step 6**: Generate post-deployment checklist

---

## What to Check on Your Hosting:

1. **Go to your hosting control panel** (cPanel/Plesk)
2. **Find "MySQL Databases"** section
3. **Note down**:
   - Existing database name (or create new)
   - Database user with full permissions
   - Database password
4. **Find "File Manager"** or note FTP root directory
5. **Check PHP Version** - Must be 8.1 or higher

Please provide the information above, and I'll proceed to Step 2.
