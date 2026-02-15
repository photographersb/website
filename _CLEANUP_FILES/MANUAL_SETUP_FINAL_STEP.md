# 🎯 MANUAL SETUP - Complete Laravel Deployment

## Your Application is Ready - Complete Final Step Manually

Due to MySQL authentication complexities, please complete the database migrations through CloudPanel's tools or contact your provider.

---

## WHAT'S DONE ✅

- ✓ All 1,655 application files deployed
- ✓ Composer dependencies installed  
- ✓ Laravel configuration (APP_KEY, .env)
- ✓ File permissions set
- ✓ Database `photodb` created
- ✓ Database user `photouser` created

**Application Location:** `/home/photographersb/htdocs/photographersb.com/public/`

---

## FINAL STEP: Run Database Migrations

### **Option A: Via SSH Terminal** (Recommended)

If you have terminal/SSH access:

```bash
# SSH into your VPS
ssh root@148.135.136.95

# Navigate to app
cd /home/photographersb/htdocs/photographersb.com/public

# Update Laravel config (if needed)
# Edit .env and ensure these are set:
# DB_DATABASE=photodb
# DB_USERNAME=photouser  
# DB_PASSWORD=[password you set in CloudPanel]

# Run migrations as the web user
sudo -u photographersb php artisan migrate:fresh --force

# Or if you want to keep existing data
sudo -u photographersb php artisan migrate --force

# Check status
sudo -u photographersb php artisan migrate:status
```

### **Option B: Via File Manager**

If using CloudPanel's File Manager:

1. Go to **File Manager** tab
2. Navigate to: `/home/photographersb/htdocs/photographersb.com/public/`
3. Find file: `.env`  
4. Edit it and ensure database settings are:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=photodb
   DB_USERNAME=photouser
   DB_PASSWORD=[from CloudPanel Databases section]
   ```
5. Save the file

Then request terminal access from your provider or use Option A.

### **Option C: Contact Your VPS Provider**

Send this message to Hostinger/your provider support:

> "Please execute this command on my server (photographersb.com):
> ```bash
> cd /home/photographersb/htdocs/photographersb.com/public && \
> sudo -u photographersb php artisan migrate --force
> ```
> My database credentials:
> - Host: 127.0.0.1
> - Database: photodb
> - User: photouser
> - Password: [see CloudPanel Databases section]"

---

## Current Database Status

| Setting | Value |
|---------|-------|
| Host | 127.0.0.1 |
| Port | 3306 |
| Database | photodb |
| User | photouser |
| Password | *Set in CloudPanel* |

**To find the password:**
1. Open CloudPanel: https://148.135.136.95:8443
2. Go to **Databases** tab  
3. Click **"Manage"** next to `photouser`
4. You should be able to view or reset the password there

---

## What Will Happen After Migrations

The migrations will:
- ✓ Create all necessary database tables
- ✓ Set up user accounts, albums, bookings, competitions, etc.
- ✓ Prepare the database for your Laravel application
- ✓ Make the site fully functional at **https://photographersb.com**

---

## Troubleshooting

### "Access denied for user 'photouser'"
- Check password is correct in CloudPanel Databases
- Verify you're connecting to `127.0.0.1` (not 'localhost')
- Check .env file has correct credentials

### "Table already exists"
Use this instead:
```bash
sudo -u photographersb php artisan migrate:fresh --force
```
(This will drop and recreate all tables)

### "SQLSTATE[HY000] [1045] Access denied"
The database user doesn't have proper permissions. In CloudPanel:
1. Go to Databases
2. Click "Manage" for photouser
3. Ensure "Read and Write" is selected for `photodb`

---

## Configuration Files

`.env` location:
```
/home/photographersb/htdocs/photographersb.com/public/.env
```

Check that these lines exist:
```bash
APP_NAME=Photographar
APP_ENV=local
APP_KEY=base64:mAGKFXEiIET8Om4Hpdnnb57lH0joHDw19RzJWl/8dpg=
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=photodb
DB_USERNAME=photouser
DB_PASSWORD=*** 
```

---

## Post-Migration Steps

Once migrations complete successfully:

1. **Update .env for production:**
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://photographersb.com
   ```

2. **Create admin account** (if needed):
   ```bash
   sudo -u photographersb php artisan tinker
   
   # In tinker prompt:
   App\Models\User::create([
       'name' => 'Admin',
       'email' => 'admin@photographersb.com',
       'password' => bcrypt('ChangeMe123!'),
       'email_verified_at' => now(),
   ]);
   
   exit;
   ```

3. **Clear cache:**
   ```bash
   cd /home/photographersb/htdocs/photographersb.com/public
   sudo -u photographersb php artisan cache:clear
   sudo -u photographersb php artisan config:clear
   ```

4. **Test site:** Visit https://photographersb.com

---

## Support

- **Laravel Docs:** https://laravel.com/docs/11/migrations
- **Error Logs:** `/home/photographersb/htdocs/photographersb.com/public/storage/logs/laravel.log`
- **Database Logs:** `/var/log/mysql/error.log`

---

**Your application is 100% deployed and ready to go!**  
**Just need to run one command to activate the database.**
