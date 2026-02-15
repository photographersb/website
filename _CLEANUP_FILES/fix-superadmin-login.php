<?php
/**
 * Fix Super Admin Login - Reset Password & Generate Token
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== FIXING SUPER ADMIN LOGIN ===\n\n";

try {
    $email = 'mahidulislamnakib@gmail.com';
    $newPassword = 'Nakib##123**';
    
    // Find or create user
    $user = User::where('email', $email)->first();

    if (!$user) {
        echo "Creating new super admin account...\n";
        $user = User::create([
            'name' => 'Mahidul Islam Nakib',
            'email' => $email,
            'password' => Hash::make($newPassword),
            'role' => 'super_admin',
            'email_verified_at' => now(),
            'approval_status' => 'approved',
            'approved_at' => now(),
            'is_suspended' => false,
            'terms_accepted_at' => now(),
        ]);
        echo "✅ Account created\n";
    } else {
        echo "Found existing user, updating password...\n";
        $user->update([
            'password' => Hash::make($newPassword),
            'role' => 'super_admin',
            'email_verified_at' => now(),
            'approval_status' => 'approved',
            'approved_at' => now(),
            'is_suspended' => false,
            'terms_accepted_at' => $user->terms_accepted_at ?? now(),
        ]);
        echo "✅ Password updated\n";
    }

    // Delete old tokens
    $user->tokens()->delete();
    
    // Generate fresh token
    $token = $user->createToken('superadmin-access', ['*'])->plainTextToken;

    echo "\n" . str_repeat("=", 70) . "\n";
    echo "✅ SUPER ADMIN LOGIN READY\n";
    echo str_repeat("=", 70) . "\n\n";

    echo "USER INFO:\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: {$user->role}\n";
    echo "  ID: {$user->id}\n\n";

    echo "LOGIN CREDENTIALS:\n";
    echo "  Email: $email\n";
    echo "  Password: $newPassword\n\n";

    echo "AUTH TOKEN:\n";
    echo "  $token\n\n";

    echo str_repeat("=", 70) . "\n";
    echo "EASIEST METHOD - Copy & Paste in Browser Console:\n";
    echo str_repeat("=", 70) . "\n\n";
    
    echo "1. Open: http://127.0.0.1:8000\n";
    echo "2. Press F12 (Developer Tools)\n";
    echo "3. Go to Console tab\n";
    echo "4. Paste this entire block:\n\n";
    
    echo "localStorage.clear();\n";
    echo "localStorage.setItem('auth_token', '$token');\n";
    echo "localStorage.setItem('user_role', 'super_admin');\n";
    echo "localStorage.setItem('user_name', '{$user->name}');\n";
    echo "localStorage.setItem('user_email', '$email');\n";
    echo "localStorage.setItem('user_id', '{$user->id}');\n";
    echo "alert('✅ Logged in as Super Admin!');\n";
    echo "window.location.href = '/admin/dashboard';\n\n";

    echo str_repeat("=", 70) . "\n";
    echo "ALTERNATIVE - Login Form:\n";
    echo str_repeat("=", 70) . "\n\n";
    echo "Go to: http://127.0.0.1:8000/admin/login\n";
    echo "Email: $email\n";
    echo "Password: $newPassword\n\n";

    // Test the password
    if (Hash::check($newPassword, $user->password)) {
        echo "✅ Password verification: SUCCESS\n";
    } else {
        echo "❌ Password verification: FAILED\n";
    }

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
?>
