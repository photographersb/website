# FTP Upload Script
Write-Host "FTP Upload for Photographer SB" -ForegroundColor Cyan
$ftpServer = "premium.bd101.svlogins.com"
$ftpUser = "photogra"
$ftpPass = Read-Host "Enter FTP Password" -AsSecureString
$ftpPassText = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($ftpPass))

function Upload-FtpFile {
    param($Local, $Remote)
    try {
        $req = [System.Net.FtpWebRequest]::Create("ftp://$ftpServer$Remote")
        $req.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPassText)
        $req.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $req.UseBinary = $true
        $content = [System.IO.File]::ReadAllBytes($Local)
        $req.ContentLength = $content.Length
        $stream = $req.GetRequestStream()
        $stream.Write($content, 0, $content.Length)
        $stream.Close()
        $resp = $req.GetResponse()
        $resp.Close()
        return $true
    } catch {
        Write-Host "  Error: $_" -ForegroundColor Red
        return $false
    }
}

function Create-FtpDir {
    param($Dir)
    try {
        $req = [System.Net.FtpWebRequest]::Create("ftp://$ftpServer$Dir")
        $req.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPassText)
        $req.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $resp = $req.GetResponse()
        $resp.Close()
    } catch { }
}

# Upload files
$files = @(
    "resources\js\components\Auth.vue=/public_html/resources/js/components/Auth.vue",
    "resources\js\components\Notification.vue=/public_html/resources/js/components/Notification.vue",
    "resources\js\utils\notifications.js=/public_html/resources/js/utils/notifications.js",
    "app\Models\User.php=/public_html/app/Models/User.php",
    "app\Notifications\VerifyEmailNotification.php=/public_html/app/Notifications/VerifyEmailNotification.php",
    "resources\js\Pages\About.vue=/public_html/resources/js/Pages/About.vue",
    "resources\js\Pages\HelpCenter.vue=/public_html/resources/js/Pages/HelpCenter.vue"
)

foreach ($f in $files) {
    $parts = $f -split "="
    $local = $parts[0]
    $remote = $parts[1]
    Write-Host "Uploading: $local" -ForegroundColor Yellow
    $dir = Split-Path $remote -Parent
    Create-FtpDir $dir
    if (Upload-FtpFile $local $remote) {
        Write-Host "  Success!" -ForegroundColor Green
    }
}

Write-Host "`nDone! Clear cache on server with:" -ForegroundColor Cyan
Write-Host "php artisan config:clear && php artisan cache:clear && php artisan view:clear" -ForegroundColor White
