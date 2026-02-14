<?php
/**
 * Login as Super Admin: mahidulislamnakib@gmail.com
 * This script will create a login token for the super admin
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== SUPER ADMIN LOGIN HELPER ===\n\n";

try {
    // Find the super admin user
    $email = 'mahidulislamnakib@gmail.com';
    $user = User::where('email', $email)->first();

    if (!$user) {
        echo "❌ User not found: $email\n";
        echo "Creating super admin account...\n\n";
        
        // Create the super admin user
        $user = User::create([
            'name' => 'Mahidul Islam Nakib',
            'email' => $email,
            'password' => Hash::make('password123'), // Default password
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);
        
        echo "✅ Super admin account created successfully!\n\n";
    }

    // Check role
    if ($user->role !== 'super_admin') {
        echo "⚠️  User exists but role is: {$user->role}\n";
        echo "Updating to super_admin...\n";
        $user->update(['role' => 'super_admin']);
        echo "✅ Role updated to super_admin\n\n";
    }

    // Generate auth token
    $token = $user->createToken('admin-access', ['*'])->plainTextToken;

    echo "USER DETAILS:\n";
    echo "  ID: {$user->id}\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: {$user->role}\n";
    echo "  Email Verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";
    echo "\n";

    echo "AUTH TOKEN (copy this):\n";
    echo "  $token\n\n";

    echo "QUICK LOGIN OPTIONS:\n\n";

    echo "1. BROWSER CONSOLE METHOD:\n";
    echo "   Open: http://127.0.0.1:8000/admin/login\n";
    echo "   Press F12, go to Console tab, paste:\n\n";
    echo "   localStorage.setItem('auth_token', '$token');\n";
    echo "   localStorage.setItem('user_role', 'super_admin');\n";
    echo "   localStorage.setItem('user_name', '{$user->name}');\n";
    echo "   localStorage.setItem('user_email', '{$user->email}');\n";
    echo "   window.location.href = '/admin/dashboard';\n\n";

    echo "2. API LOGIN METHOD:\n";
    echo "   Email: $email\n";
    echo "   Password: password123\n\n";

    echo "3. DIRECT ACCESS (after running browser console):\n";
    echo "   http://127.0.0.1:8000/admin/dashboard\n\n";

    echo str_repeat("=", 60) . "\n";
    echo "✅ Ready to login as Super Admin!\n";
    echo str_repeat("=", 60) . "\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
