<?php
// Server diagnostic - check why login is failing
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SERVER LOGIN DIAGNOSTIC ===\n\n";

// Check users
$users = \App\Models\User::whereIn('role', ['super_admin', 'admin'])->get();
echo "Admin users found: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "User ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    echo "Email Verified: " . ($user->email_verified_at ? 'YES' : 'NO') . "\n";
    echo "Suspended: " . ($user->is_suspended ? 'YES' : 'NO') . "\n";
    
    // Test password with multiple attempts
    $testPasswords = ['password', 'password123', 'Password123'];
    echo "Password tests:\n";
    foreach ($testPasswords as $pass) {
        $result = \Illuminate\Support\Facades\Hash::check($pass, $user->password);
        echo "  - '{$pass}': " . ($result ? '✓ MATCH' : '✗ no match') . "\n";
    }
    echo "\n";
}

// Generate working password
$newHash = \Illuminate\Support\Facades\Hash::make('admin123');
echo "=== WORKING SQL FIX ===\n";
echo "UPDATE users SET password = '{$newHash}' WHERE id = 1;\n";
echo "\nThen login with: admin123\n";
?>
