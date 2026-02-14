# DEPLOYMENT GUIDE FOR NEW BDIX HOSTING
# Domain: photographersb.com
# Hosting: premium.bd101.svlogins.com

## STEP 1: Create Database (Do this first!)

1. In cPanel → MySQL Databases
2. Create database: photogra_photographersb
3. Create user: photogra_dbuser (with strong password)
4. Add user to database with ALL PRIVILEGES
5. Write down credentials!

## STEP 2: Prepare Files for Upload

Your deployment structure for public_html:

```
public_html/
├── index.php (from public folder)
├── .htaccess (from public folder)
├── build/ (from public folder)
├── storage/ (symlink - from public folder)
├── app/ (Laravel app folder)
├── bootstrap/ (Laravel bootstrap)
├── config/ (Laravel config)
├── database/ (migrations/seeds)
├── resources/ (views/assets)
├── routes/ (routes)
├── vendor/ (dependencies)
├── .env (production config)
├── artisan (CLI tool)
└── composer.json
```

## STEP 3: Upload Process

### Method 1: Via ZIP Upload (Recommended)

1. On your LOCAL computer:
   - Go to: C:\xampp\htdocs\Photographar SB\
   - I'll create a special public_html package

2. Upload to server:
   - cPanel File Manager → public_html folder
   - Upload ZIP
   - Extract
   - Delete ZIP

### Method 2: Via FTP

1. Upload ALL files from deployment-package to public_html
2. Move public/* contents to public_html root
3. Delete empty public folder

## STEP 4: Configure .env

Update these in .env:
- DB_DATABASE=photogra_photographersb
- DB_USERNAME=photogra_dbuser  
- DB_PASSWORD=your_db_password
- APP_URL=https://photographersb.com

## STEP 5: Import Database

1. cPanel → phpMyAdmin
2. Select your database
3. Import → Choose database.sql file
4. Execute

## STEP 6: Set Permissions

Folders need 755 permissions:
- storage/
- bootstrap/cache/

## READY?

Let me know your database credentials and I'll create the final production .env file!
