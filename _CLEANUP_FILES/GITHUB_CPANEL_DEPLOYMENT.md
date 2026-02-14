# 🚀 GitHub to cPanel Deployment Guide

## Benefits
- ✅ One-click deployment from GitHub
- ✅ Automatic file sync
- ✅ Version control for rollback
- ✅ No manual FTP uploads
- ✅ Team collaboration ready

---

## 📋 Setup Steps

### **Step 1: Create GitHub Repository**

1. Go to https://github.com/new
2. Repository name: `photographar-sb`
3. Privacy: **Private** (recommended)
4. Click **Create repository**

### **Step 2: Initialize Git Locally**

```bash
cd "c:\xampp\htdocs\Photographar SB"

# Initialize git
git init

# Add remote
git remote add origin https://github.com/YOUR_USERNAME/photographar-sb.git

# Create .gitignore (if not exists)
# Add to .gitignore:
node_modules/
vendor/
.env
.env.production
storage/logs/
storage/framework/cache/
storage/framework/sessions/
storage/framework/views/
public/storage
public/hot
*.log
.DS_Store
Thumbs.db

# Add all files
git add .

# First commit
git commit -m "Initial commit - Photographar SB platform"

# Push to GitHub
git branch -M main
git push -u origin main
```

### **Step 3: Setup cPanel Git Deployment**

#### **Via cPanel Git Version Control:**

1. **Login to cPanel**: https://premium.bd101.svlogins.com:2083

2. **Navigate to Git™ Version Control**
   - Search for "Git" in cPanel search
   - Click "Git Version Control"

3. **Create New Repository**
   - Click **"Create"**
   - Fill in:
     - **Clone URL**: `https://github.com/YOUR_USERNAME/photographar-sb.git`
     - **Repository Path**: `/home/photogra/repositories/photographar-sb`
     - **Repository Name**: `photographar-sb`
   - Click **Create**

4. **Pull to Live Site**
   - Click **"Manage"** on your repository
   - Click **"Pull or Deploy"** tab
   - **Deployment Path**: `/home/photogra/public_html`
   - Click **"Update from Remote"**
   - ✅ Files deployed!

#### **Via SSH (Alternative Method):**

1. **SSH into Server**:
```bash
ssh photogra@premium.bd101.svlogins.com
```

2. **Navigate to public_html**:
```bash
cd /home/photogra/public_html
```

3. **Clone or Pull**:

**First time (fresh deployment):**
```bash
# Backup existing files
cd /home/photogra
mv public_html public_html_backup

# Clone repository
git clone https://github.com/YOUR_USERNAME/photographar-sb.git public_html
cd public_html
```

**Subsequent deployments:**
```bash
cd /home/photogra/public_html
git pull origin main
```

4. **Post-Deployment Steps**:
```bash
# Install/update dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R photogra:photogra storage bootstrap/cache

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations (if needed)
php artisan migrate --force
```

---

## 🔄 Deployment Workflow

### **Every Time You Make Changes:**

```bash
# Local machine
cd "c:\xampp\htdocs\Photographar SB"

# Make your changes...
# Then commit and push:

git add .
git commit -m "Added password visibility toggle and forgot password feature"
git push origin main
```

### **Deploy to Production:**

**Method A: cPanel Git (One Click)**
1. Login to cPanel → Git Version Control
2. Click **"Manage"** on repository
3. Click **"Pull or Deploy"** → **"Update from Remote"**
4. Done! ✅

**Method B: SSH**
```bash
ssh photogra@premium.bd101.svlogins.com
cd /home/photogra/public_html
git pull origin main
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:clear
php artisan cache:clear
```

---

## 🔐 GitHub Personal Access Token (Required)

GitHub requires a token instead of password for Git operations.

### **Create Token:**

1. Go to: https://github.com/settings/tokens
2. Click **"Generate new token (classic)"**
3. Name: `cPanel Deployment`
4. Scopes: Select **`repo`** (full control of private repositories)
5. Click **Generate token**
6. **COPY THE TOKEN** (you won't see it again!)

### **Use Token in cPanel:**

When cPanel asks for credentials:
- **Username**: Your GitHub username
- **Password**: Paste the token (not your GitHub password)

Or use in clone URL:
```
https://YOUR_USERNAME:YOUR_TOKEN@github.com/YOUR_USERNAME/photographar-sb.git
```

---

## 📦 .gitignore Configuration

Ensure your `.gitignore` includes:

```gitignore
# Dependencies
/node_modules
/vendor

# Environment files
.env
.env.production
.env.backup
.env.*.php
.env.php

# Storage
/storage/*.key
/storage/logs/*
/storage/framework/cache/*
/storage/framework/sessions/*
/storage/framework/views/*
!/storage/framework/.gitignore
!/storage/logs/.gitignore

# Public storage
/public/storage
/public/hot

# Build files (if you want to build on server)
# /public/build/*
# !/public/build/.gitignore

# IDE
.idea/
.vscode/
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db

# Logs
*.log
npm-debug.log*
yarn-debug.log*
yarn-error.log*

# Testing
.phpunit.result.cache
coverage/

# Deployment
deployment-package.zip
PhotographerSB_COMPLETE.zip
```

---

## 🎯 Deploy Build Files

You have **two options** for handling build files:

### **Option A: Commit Build Files (Simpler)**

```bash
# Remove build/ from .gitignore
# Comment out or remove this line in .gitignore:
# /public/build/

# Commit build files
git add public/build/
git commit -m "Add production build files"
git push origin main
```

**Pros**: No need to run `npm run build` on server  
**Cons**: Larger repository size

### **Option B: Build on Server (Cleaner)**

Keep `/public/build/` in `.gitignore`, build after deployment:

```bash
# After git pull on server:
npm install
npm run build
```

**Pros**: Cleaner repository  
**Cons**: Requires Node.js on server

---

## 🔄 Automated Deployment (Advanced)

### **Setup Webhook for Auto-Deploy:**

1. **Create Deployment Script** on server:

```bash
# Create: /home/photogra/deploy.sh
#!/bin/bash

cd /home/photogra/public_html
git pull origin main
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan config:clear
php artisan cache:clear
php artisan route:clear
echo "Deployment completed at $(date)" >> /home/photogra/deploy.log
```

2. **Make executable**:
```bash
chmod +x /home/photogra/deploy.sh
```

3. **Setup GitHub Webhook**:
   - GitHub repo → Settings → Webhooks → Add webhook
   - Payload URL: `https://photographersb.com/deploy.php`
   - Content type: `application/json`
   - Secret: Generate a secret key
   - Events: Just the push event

4. **Create deploy.php** in `public_html/`:
```php
<?php
$secret = 'your-webhook-secret';
$payload = file_get_contents('php://input');
$signature = hash_hmac('sha256', $payload, $secret);

if (hash_equals('sha256=' . $signature, $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '')) {
    shell_exec('/home/photogra/deploy.sh > /dev/null 2>&1 &');
    http_response_code(200);
    echo 'Deployment triggered';
} else {
    http_response_code(403);
    echo 'Invalid signature';
}
```

Now every `git push` automatically deploys! 🎉

---

## 🐛 Troubleshooting

### **"Authentication failed"**
- Use GitHub Personal Access Token, not password
- Check token has `repo` scope

### **"Permission denied"**
```bash
chmod -R 755 /home/photogra/public_html
chown -R photogra:photogra /home/photogra/public_html
```

### **"Composer not found"**
```bash
# Use full path
/usr/local/bin/composer install --optimize-autoloader --no-dev
```

### **"npm not found"**
- Install Node.js via cPanel or SSH
- Or commit build files (Option A above)

### **Deployment but site shows old version**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
# Clear browser cache: Ctrl+Shift+Delete
```

---

## 📊 Recommended Workflow

### **Development:**
```bash
# Work locally
cd "c:\xampp\htdocs\Photographar SB"
# Make changes...
npm run dev  # Test locally
git add .
git commit -m "Feature: Password reset"
git push origin main
```

### **Staging (Optional):**
```bash
# Create staging branch
git checkout -b staging
git push origin staging
# Deploy staging branch to test subdomain
```

### **Production:**
```bash
# Merge to main
git checkout main
git merge staging
git push origin main
# Auto-deploys via webhook or manual pull in cPanel
```

---

## ✅ Quick Checklist

- [ ] Create GitHub repository (private)
- [ ] Initialize git locally
- [ ] Configure .gitignore
- [ ] Push to GitHub
- [ ] Setup cPanel Git or SSH access
- [ ] Create GitHub Personal Access Token
- [ ] Clone/pull repository on server
- [ ] Set up post-deployment script
- [ ] Test deployment
- [ ] (Optional) Setup webhook automation

---

**GitHub to cPanel deployment is now ready! 🚀**

Every `git push` → Pull in cPanel → Site updated!
