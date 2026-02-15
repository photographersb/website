#!/usr/bin/env powershell
# Photographar SB - GitHub to cPanel Deployment Script
# This script will generate SSH keys, display them, and help deploy to cPanel

Write-Host "=== PHOTOGRAPHAR SB - SSH KEY GENERATOR ===" -ForegroundColor Cyan
Write-Host "This script will help you deploy from GitHub to cPanel via SSH" -ForegroundColor Yellow
Write-Host ""

# Step 1: Connect to server
Write-Host "Step 1: Connecting to your server..." -ForegroundColor Magenta
Write-Host "Server: premium.bd101.svlogins.com" -ForegroundColor Gray
Write-Host "Username: photogra" -ForegroundColor Gray
Write-Host ""
Write-Host "Running: ssh photogra@premium.bd101.svlogins.com" -ForegroundColor Green

# Open SSH session
$server = "photogra@premium.bd101.svlogins.com"

Write-Host ""
Write-Host "Step 2: Generating SSH key on server..." -ForegroundColor Magenta
Write-Host ""

# Generate SSH key
ssh $server "ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa -N """
Write-Host "✓ SSH key generated" -ForegroundColor Green

Write-Host ""
Write-Host "Step 3: Retrieving your public key..." -ForegroundColor Magenta
Write-Host ""

# Get public key
$publicKey = ssh $server "cat ~/.ssh/id_rsa.pub"

Write-Host "Your SSH Public Key:" -ForegroundColor Cyan
Write-Host "===================" -ForegroundColor Cyan
Write-Host $publicKey -ForegroundColor Yellow
Write-Host "===================" -ForegroundColor Cyan

Write-Host ""
Write-Host "⚠️  IMPORTANT: Copy the key above!" -ForegroundColor Red
Read-Host "Press Enter when you've copied the key"

Write-Host ""
Write-Host "Step 4: Open GitHub to add the key..." -ForegroundColor Magenta
Write-Host "1. Go to: https://github.com/settings/keys" -ForegroundColor White
Write-Host "2. Click 'New SSH key'" -ForegroundColor White
Write-Host "3. Title: cPanel Server" -ForegroundColor White
Write-Host "4. Paste the key above" -ForegroundColor White
Write-Host "5. Click 'Add SSH key'" -ForegroundColor White
Write-Host ""
Read-Host "Press Enter when you've added the SSH key to GitHub"

Write-Host ""
Write-Host "Step 5: Deploying repository to cPanel..." -ForegroundColor Magenta
Write-Host ""

# Run deployment commands
ssh $server @"
cd /home/photogra
mv public_html public_html_backup
git clone git@github.com:mahidulislamnakib/photographer-sb.git public_html
cd public_html
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan config:clear
php artisan cache:clear
php artisan route:clear
chmod -R 755 storage bootstrap/cache public/build
chown -R photogra:photogra storage bootstrap/cache public/build
echo "✓ Deployment Complete!"
"@

Write-Host ""
Write-Host "✅ DEPLOYMENT COMPLETE!" -ForegroundColor Green
Write-Host ""
Write-Host "Your site is now live at: https://photographersb.com" -ForegroundColor Cyan
Write-Host ""
Write-Host "Future updates are easy:" -ForegroundColor Yellow
Write-Host "1. Make changes locally" -ForegroundColor White
Write-Host "2. git push origin main" -ForegroundColor White
Write-Host "3. ssh photogra@premium.bd101.svlogins.com" -ForegroundColor White
Write-Host "4. cd /home/photogra/public_html && git pull origin main" -ForegroundColor White
