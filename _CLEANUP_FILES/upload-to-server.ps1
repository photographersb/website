# FTP Upload Script for Photographer SB
# This uploads all necessary files to the production server

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "PHOTOGRAPHER SB - FTP UPLOAD" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# FTP Configuration
$ftpServer = "ftp://premium.bd101.svlogins.com"
$ftpUsername = "photogra"
$ftpPassword = Read-Host "Enter FTP Password" -AsSecureString
$ftpPasswordText = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($ftpPassword))

# Local path
$localPath = "c:\xampp\htdocs\Photographar SB"

Write-Host "Preparing upload package..." -ForegroundColor Yellow
Write-Host ""

# Use WinSCP for reliable upload
$winscp = Test-Path "C:\Program Files (x86)\WinSCP\WinSCP.com"

if (-not $winscp) {
    Write-Host "WinSCP not found. Using built-in FTP (slower)..." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "For faster uploads, download WinSCP from: https://winscp.net/eng/download.php" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Manual Upload Instructions:" -ForegroundColor Green
    Write-Host "1. Download FileZilla: https://filezilla-project.org/download.php?type=client" -ForegroundColor White
    Write-Host "2. Connect to: premium.bd101.svlogins.com" -ForegroundColor White
    Write-Host "3. Username: photogra" -ForegroundColor White
    Write-Host "4. Port: 21" -ForegroundColor White
    Write-Host "5. Delete everything in /public_html/" -ForegroundColor White
    Write-Host "6. Upload ALL files from: $localPath" -ForegroundColor White
    Write-Host "   EXCEPT: node_modules, .git folders" -ForegroundColor White
    Write-Host ""
    Write-Host "Press any key to open FileZilla download page..." -ForegroundColor Yellow
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
    Start-Process "https://filezilla-project.org/download.php?type=client"
    exit
}

Write-Host "Using WinSCP for upload..." -ForegroundColor Green
Write-Host "This may take 10-15 minutes..." -ForegroundColor Yellow
Write-Host ""

# Create WinSCP script
$scriptPath = Join-Path $env:TEMP "winscp-upload.txt"
@"
option batch abort
option confirm off
open ftp://${ftpUsername}:${ftpPasswordText}@premium.bd101.svlogins.com
option transfer binary
cd /public_html
rm *
lcd "$localPath"
put * -filemask="|node_modules/;.git/;vendor/;storage/logs/;.env"
mkdir storage
cd storage
put storage/* -filemask="|logs/"
cd ..
put .env
exit
"@ | Out-File -FilePath $scriptPath -Encoding ASCII

& "C:\Program Files (x86)\WinSCP\WinSCP.com" /script=$scriptPath

Remove-Item $scriptPath

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "UPLOAD COMPLETE!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps - Run these in cPanel Terminal:" -ForegroundColor Yellow
Write-Host "cd /home/photogra/public_html" -ForegroundColor White
Write-Host "composer install --optimize-autoloader --no-dev" -ForegroundColor White
Write-Host "php artisan config:clear" -ForegroundColor White
Write-Host "php artisan cache:clear" -ForegroundColor White
Write-Host "chmod -R 775 storage bootstrap/cache" -ForegroundColor White
Write-Host ""
