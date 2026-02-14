# WinSCP automated upload for Photographer SB
# Requires WinSCP installed

$projectRoot = "C:\xampp\htdocs\Photographar SB"
$remoteRoot = "/public_html"
$ftpHost = "premium.bd101.svlogins.com"
$user = "photogra"

$pass = Read-Host "Enter FTP Password" -AsSecureString
$passPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($pass))

$winScpPaths = @(
    "C:\Program Files\WinSCP\WinSCP.com",
    "C:\Program Files (x86)\WinSCP\WinSCP.com",
    "$env:LOCALAPPDATA\Programs\WinSCP\WinSCP.com"
)

$winScpPath = $winScpPaths | Where-Object { Test-Path $_ } | Select-Object -First 1
if (-not $winScpPath) {
    Write-Host "WinSCP.com not found. Install WinSCP first." -ForegroundColor Red
    exit 1
}

$scriptContent = @"
option batch abort
option confirm off
open "ftp://${user}:${passPlain}@${ftpHost}/" -passive=on
lcd "$projectRoot"
cd "$remoteRoot"

# Upload build assets and updated files
synchronize remote "public\build" "$remoteRoot/public/build"
put "resources\js\components\Auth.vue" "$remoteRoot/resources/js/components/Auth.vue"
put "resources\js\components\Notification.vue" "$remoteRoot/resources/js/components/Notification.vue"
put "resources\js\components\PaymentSuccess.vue" "$remoteRoot/resources/js/components/PaymentSuccess.vue"
put "resources\js\utils\notifications.js" "$remoteRoot/resources/js/utils/notifications.js"
synchronize remote "resources\views\emails" "$remoteRoot/resources/views/emails"
put "resources\views\app.blade.php" "$remoteRoot/resources/views/app.blade.php"
put "resources\js\Pages\About.vue" "$remoteRoot/resources/js/Pages/About.vue"
put "resources\js\Pages\HelpCenter.vue" "$remoteRoot/resources/js/Pages/HelpCenter.vue"
put "resources\js\Pages\Terms.vue" "$remoteRoot/resources/js/Pages/Terms.vue"
put "resources\js\Pages\Privacy.vue" "$remoteRoot/resources/js/Pages/Privacy.vue"
put "resources\js\Pages\Admin\Settings\Index.vue" "$remoteRoot/resources/js/Pages/Admin/Settings/Index.vue"
put "public\site.webmanifest" "$remoteRoot/public/site.webmanifest"
put "public\admin-sitemap.html" "$remoteRoot/public/admin-sitemap.html"
put "app\Models\User.php" "$remoteRoot/app/Models/User.php"
put "app\Notifications\VerifyEmailNotification.php" "$remoteRoot/app/Notifications/VerifyEmailNotification.php"
put "app\Services\CertificateService.php" "$remoteRoot/app/Services/CertificateService.php"

exit
"@

$tempScript = Join-Path $env:TEMP "winscp-upload.txt"
$logFile = Join-Path $env:TEMP "winscp-upload.log"

Set-Content -Path $tempScript -Value $scriptContent -Encoding UTF8

& $winScpPath /script=$tempScript /log=$logFile
$exitCode = $LASTEXITCODE

Remove-Item $tempScript -ErrorAction SilentlyContinue

if ($exitCode -eq 0) {
    Write-Host "Upload complete." -ForegroundColor Green
    Write-Host "Clear cache on server:" -ForegroundColor Yellow
    Write-Host "php artisan config:clear" -ForegroundColor White
    Write-Host "php artisan cache:clear" -ForegroundColor White
    Write-Host "php artisan view:clear" -ForegroundColor White
} else {
    Write-Host "Upload failed. Check log: $logFile" -ForegroundColor Red
    exit $exitCode
}
