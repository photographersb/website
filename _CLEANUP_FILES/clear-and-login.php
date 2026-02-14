<?php
/**
 * Emergency Login & Password Reset Script
 * - Clears rate limiter cache
 * - Shows current password hash
 * - Resets password to 'password123'
 * - Generates fresh authentication token
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

echo "=== EMERGENCY LOGIN RECOVERY ===\n\n";

// 1. Clear rate limiter cache
echo "1. Clearing rate limiter cache...\n";
Cache::flush();
echo "   ✓ Cache cleared\n\n";

// 2. Find super admin user
$user = User::where('email', 'mahidulislamnakib@gmail.com')->first();

if (!$user) {
    die("ERROR: User not found!\n");
}

echo "2. User Found:\n";
echo "   ID: {$user->id}\n";
echo "   Name: {$user->name}\n";
echo "   Email: {$user->email}\n";
echo "   Role: {$user->role}\n\n";

// 3. Show current password hash
echo "3. Current Password Hash:\n";
echo "   {$user->password}\n\n";

// 4. Reset password to known value
$newPassword = 'password123';
$user->password = Hash::make($newPassword);
$user->save();

echo "4. Password Reset Complete!\n";
echo "   New Password: {$newPassword}\n";
echo "   New Hash: {$user->password}\n\n";

// 5. Test the password
if (Hash::check($newPassword, $user->password)) {
    echo "   ✓ Password verification: SUCCESS\n\n";
} else {
    echo "   ✗ Password verification: FAILED\n\n";
}

// 6. Generate fresh authentication token
$user->tokens()->delete(); // Delete old tokens
$token = $user->createToken('emergency-access')->plainTextToken;

echo "5. Fresh Authentication Token Generated:\n";
echo "   {$token}\n\n";

echo "=== RECOVERY OPTIONS ===\n\n";

echo "OPTION A - Token Login (Recommended):\n";
echo "1. Open browser console (F12)\n";
echo "2. Go to Application > Local Storage\n";
echo "3. Run these commands:\n";
echo "   localStorage.setItem('auth_token', '{$token}');\n";
echo "   localStorage.setItem('auth_user', JSON.stringify(" . json_encode([
    'id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'role' => $user->role
]) . "));\n";
echo "   location.reload();\n\n";

echo "OPTION B - Password Login:\n";
echo "   Email: mahidulislamnakib@gmail.com\n";
echo "   Password: {$newPassword}\n\n";

echo "=== SQL UPDATE (if needed) ===\n\n";
echo "UPDATE users SET password = '{$user->password}' WHERE id = {$user->id};\n\n";

echo "✓ Recovery script complete!\n";
?>

