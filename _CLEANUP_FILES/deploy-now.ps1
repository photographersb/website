#!/usr/bin/env powershell
# Automatic Deployment Script - GitHub to cPanel

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "PHOTOGRAPHAR SB - AUTO DEPLOYMENT" -ForegroundColor Magenta
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$server = "photogra@premium.bd101.svlogins.com"

Write-Host "Connecting to server: $server" -ForegroundColor Yellow
Write-Host "This will take 5-10 minutes..." -ForegroundColor Gray
Write-Host ""

# Run deployment
ssh $server @'
cd /home/photogra && \
mv public_html public_html_backup && \
git clone git@github.com:mahidulislamnakib/photographer-sb.git public_html && \
cd public_html && \
composer install --optimize-autoloader --no-dev && \
npm install && \
npm run build && \
php artisan config:clear && \
php artisan cache:clear && \
php artisan route:clear && \
chmod -R 755 storage bootstrap/cache && \
chown -R photogra:photogra storage bootstrap/cache && \
echo "✅ DEPLOYMENT COMPLETE!"
'@

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "✅ DEPLOYMENT FINISHED!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Your site is now live at:" -ForegroundColor Cyan
Write-Host "https://photographersb.com" -ForegroundColor Yellow
Write-Host ""
Write-Host "Check your site in a moment!" -ForegroundColor Green
