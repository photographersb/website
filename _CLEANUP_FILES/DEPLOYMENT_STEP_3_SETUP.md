# DEPLOYMENT STEP 3: Final Configuration
# Your files are already uploaded to: /home/ichattogram/photographersb.com

## CRITICAL STEPS TO COMPLETE:

### 1. Upload/Update .env File

**Option A: Via cPanel File Manager**
1. In File Manager, navigate to: `photographersb.com` folder
2. Click "Upload" button
3. Upload your `.env.production` file
4. After upload, rename `.env.production` to `.env`
5. Right-click `.env` → Edit
6. Add your database password on line: `DB_PASSWORD=YOUR_PASSWORD_HERE`
7. Save file

**Option B: Create new .env file directly**
1. In `photographersb.com` folder, click "+ File"
2. Name it: `.env`
3. Right-click → Edit
4. Copy contents from `c:\xampp\htdocs\Photographar SB\.env.production`
5. Paste and add your database password
6. Save

### 2. Generate Application Key
You need SSH/Terminal access. In cPanel:
1. Search for "Terminal" in cPanel
2. If available, run:
```bash
cd photographersb.com
php artisan key:generate
```

**If no Terminal access:** I'll show you how to generate key manually.

### 3. Set Correct Permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R your_username:your_username storage bootstrap/cache
```

### 4. Point Domain to Public Folder
**IMPORTANT:** Your domain must point to `photographersb.com/public`, not root.

In cPanel:
1. Search "Domains" or "Addon Domains"
2. Find `photographersb.com`
3. Edit Document Root to: `/home/ichattogram/photographersb.com/public`

### 5. Import Database
1. cPanel → phpMyAdmin
2. Select database: `ichattogram_photographersb`
3. Click "Import"
4. Upload SQL file (I'll create this next)

### 6. Run Migrations
```bash
php artisan migrate --force
php artisan db:seed --force
```

---

## NEXT: Tell me if you have Terminal/SSH access

Check cPanel for:
- [ ] Terminal icon
- [ ] SSH Access
- [ ] Can you run commands?

If YES → I'll give you exact commands
If NO → I'll create manual setup instructions
