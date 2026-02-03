# cPanel Deployment Guide for Photographar SB
**Domain:** https://photographersb.com/

## Pre-Deployment Checklist

### 1. Environment Configuration

Create/Update `.env` file for production:

```env
APP_NAME="Photographar"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://photographersb.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@photographersb.com
MAIL_FROM_NAME="${APP_NAME}"

# Payment Gateways (Configure before going live)
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
STRIPE_WEBHOOK_SECRET=your_webhook_secret

# Social Media (Optional)
FACEBOOK_APP_ID=
GOOGLE_CLIENT_ID=
```

### 2. Build Frontend Assets

Run this command locally before uploading:

```bash
npm run build
```

This creates optimized files in `public/build/` directory.

## cPanel Deployment Steps

### Step 1: Database Setup

1. **Login to cPanel**
2. **Create MySQL Database:**
   - Go to "MySQL Databases"
   - Create new database: `photographersb_main`
   - Create user: `photographersb_user`
   - Set strong password
   - Add user to database with ALL PRIVILEGES

3. **Import Database (Optional):**
   - Go to phpMyAdmin
   - Select your database
   - Import your local database SQL file (if you have data to migrate)

### Step 2: File Upload

#### Option A: File Manager (Small sites)

1. Go to cPanel File Manager
2. Navigate to `public_html` folder
3. Upload all files EXCEPT:
   - `node_modules/` (too large)
   - `.git/` (not needed)
   - `storage/logs/*` (will be regenerated)

#### Option B: FTP (Recommended)

1. Use FileZilla or other FTP client
2. Connect using cPanel FTP credentials
3. Upload entire project to `/home/your_username/photographersb/`
4. **Important:** Keep Laravel files outside `public_html` for security

### Step 3: Directory Structure (Recommended)

```
/home/your_username/
├── photographersb/              (Laravel root - OUTSIDE public_html)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   ├── artisan
│   └── composer.json
│
└── public_html/                 (Document root - ONLY public files)
    ├── build/                   (From public/build)
    ├── images/                  (From public/images)
    ├── .htaccess
    ├── index.php                (Modified - see below)
    └── robots.txt
```

### Step 4: Modify public/index.php

If Laravel is outside public_html, update `public_html/index.php`:

```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Update these paths to point to your Laravel installation
require __DIR__.'/../photographersb/vendor/autoload.php';

$app = require_once __DIR__.'/../photographersb/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

### Step 5: File Permissions

Using File Manager or SSH:

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

### Step 6: Install Composer Dependencies

#### Option A: Terminal/SSH (Recommended)

```bash
cd /home/your_username/photographersb
composer install --optimize-autoloader --no-dev
```

#### Option B: No SSH Access

1. Run locally: `composer install --optimize-autoloader --no-dev`
2. Upload the `vendor/` folder via FTP

### Step 7: Run Artisan Commands

```bash
cd /home/your_username/photographersb

# Generate application key
php artisan key:generate

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Clear and cache routes
php artisan route:clear
php artisan route:cache

# Clear and cache views
php artisan view:clear
php artisan view:cache

# Run migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Optimize application
php artisan optimize
```

### Step 8: Configure .htaccess

Create/Update `public_html/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Disable directory browsing
Options -Indexes

# Prevent viewing of .env file
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

### Step 9: SSL Certificate

1. Go to cPanel "SSL/TLS"
2. Use "Let's Encrypt" (Free) or install your certificate
3. Enable "Force HTTPS Redirect"

Or add to `.htaccess`:

```apache
# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Step 10: Setup Cron Jobs

For scheduled tasks (email notifications, reminders):

1. Go to cPanel "Cron Jobs"
2. Add this cron:

```
* * * * * cd /home/your_username/photographersb && php artisan schedule:run >> /dev/null 2>&1
```

Or if PHP path is needed:

```
* * * * * /usr/local/bin/php /home/your_username/photographersb/artisan schedule:run >> /dev/null 2>&1
```

### Step 11: Queue Workers (Optional - for better performance)

If your cPanel supports Supervisor or background processes:

```
php artisan queue:work --daemon
```

Or use cron (runs every minute):

```
* * * * * cd /home/your_username/photographersb && php artisan queue:work --stop-when-empty
```

## Post-Deployment Verification

### Test These URLs:

- ✅ Homepage: https://photographersb.com/
- ✅ Admin Login: https://photographersb.com/auth
- ✅ API Health: https://photographersb.com/api/v1/health
- ✅ Photographer Listing: https://photographersb.com/photographers
- ✅ Admin Dashboard: https://photographersb.com/admin/dashboard

### Check These Features:

1. **User Registration/Login**
   - Create test account
   - Verify email notifications

2. **Photographer Profile**
   - View profile pages
   - Check image loading

3. **Booking System**
   - Create inquiry
   - Test booking flow

4. **Admin Panel**
   - Login as admin
   - Check dashboard stats
   - Verify all admin functions

5. **Payment Gateways**
   - Test Razorpay integration
   - Test Stripe integration
   - Verify webhook endpoints

6. **File Uploads**
   - Test profile photo upload
   - Test portfolio uploads
   - Verify storage permissions

## Troubleshooting

### 500 Internal Server Error

1. Check `.env` file exists and is configured
2. Check file permissions (755 for folders, 644 for files)
3. Check error logs in `storage/logs/laravel.log`
4. Run `php artisan config:clear`

### Assets Not Loading

1. Verify `npm run build` was run
2. Check `public/build/` folder exists
3. Verify `.htaccess` rules
4. Clear browser cache

### Database Connection Failed

1. Verify database credentials in `.env`
2. Check database user has permissions
3. Ensure DB_HOST is `localhost` (not 127.0.0.1)

### 404 on All Routes

1. Check `.htaccess` file exists
2. Verify mod_rewrite is enabled
3. Check document root points to public folder

### Storage/Upload Errors

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
php artisan storage:link
```

### Email Not Sending

1. Verify MAIL_* settings in `.env`
2. Test SMTP credentials
3. Check spam folder
4. Verify port 587 or 465 is not blocked

## Performance Optimization

### 1. Enable OPcache (cPanel PHP Settings)

1. Go to "Select PHP Version"
2. Enable "opcache" extension
3. Save

### 2. Cache Configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Enable Gzip Compression

Add to `.htaccess`:

```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>
```

### 4. Browser Caching

Add to `.htaccess`:

```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

## Security Checklist

- ✅ `APP_DEBUG=false` in production
- ✅ Strong `APP_KEY` generated
- ✅ `.env` file protected (not in public_html)
- ✅ SSL certificate installed
- ✅ Force HTTPS enabled
- ✅ Database user has limited permissions
- ✅ File permissions set correctly (755/644)
- ✅ Remove `.git` folder from server
- ✅ Disable directory browsing
- ✅ Regular backups configured

## Backup Strategy

### Automated Backups (cPanel)

1. Go to "Backup Wizard"
2. Enable automatic backups
3. Backup frequency: Daily
4. Retention: 7 days

### Manual Backup

```bash
# Database
mysqldump -u username -p database_name > backup.sql

# Files
tar -czf backup.tar.gz /home/your_username/photographersb
```

## Monitoring

### 1. Error Monitoring

Check daily: `storage/logs/laravel.log`

### 2. Performance Monitoring

- Monitor database queries
- Check storage usage
- Monitor bandwidth

### 3. Uptime Monitoring

Use services like:
- UptimeRobot
- Pingdom
- StatusCake

## Quick Commands Reference

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan optimize

# View routes
php artisan route:list

# Database operations
php artisan migrate --force
php artisan db:seed --force

# Queue operations
php artisan queue:work
php artisan queue:restart

# Maintenance mode
php artisan down --secret="your-secret-token"
php artisan up
```

## Support & Resources

- Laravel Docs: https://laravel.com/docs
- cPanel Docs: https://docs.cpanel.net/
- Project Documentation: Check `docs/` folder

## Deployment Checklist

- [ ] Local backup created
- [ ] Database created on cPanel
- [ ] Files uploaded to server
- [ ] Composer dependencies installed
- [ ] `.env` file configured
- [ ] `APP_KEY` generated
- [ ] Migrations run
- [ ] Storage link created
- [ ] File permissions set
- [ ] SSL certificate installed
- [ ] Cron jobs configured
- [ ] All URLs tested
- [ ] Email sending tested
- [ ] Payment gateways tested
- [ ] Admin panel accessible
- [ ] Error logs checked
- [ ] Performance optimized
- [ ] Backups configured

---

**Deployment Date:** _____________
**Deployed By:** _____________
**Notes:** _____________
