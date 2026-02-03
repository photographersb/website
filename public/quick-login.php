<?php
/**
 * Quick Login Helper - For Development Only
 * Creates a session and auth token for testing
 */

// Start at Laravel root
chdir(__DIR__ . '/..');
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Get admin user
$admin = User::where('email', 'admin@photographar.com')->first();

if (!$admin) {
    die('Admin user not found! Run: php artisan db:seed --class=TestUsersSeeder');
}

// Create a personal access token
$token = $admin->createToken('admin-dashboard')->plainTextToken;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Quick Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold mb-6">🔐 Admin Quick Login</h1>
        
        <div class="bg-green-50 border border-green-200 rounded p-4 mb-6">
            <p class="font-semibold text-green-900 mb-2">✅ Authentication Token Generated!</p>
            <p class="text-sm text-green-700">Click the button below to auto-login to the admin dashboard.</p>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Your Auth Token:</label>
            <code class="block bg-gray-100 p-3 rounded text-xs overflow-x-auto"><?= htmlspecialchars($token) ?></code>
        </div>

        <button onclick="autoLogin()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded">
            🚀 Login & Go to Admin Dashboard
        </button>

        <div class="mt-6 text-sm text-gray-600">
            <p><strong>Email:</strong> <?= htmlspecialchars($admin->email) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($admin->role) ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($admin->name) ?></p>
        </div>
    </div>

    <script>
        function autoLogin() {
            // Store token
            localStorage.setItem('auth_token', '<?= $token ?>');
            localStorage.setItem('user', JSON.stringify({
                id: <?= $admin->id ?>,
                name: '<?= addslashes($admin->name) ?>',
                email: '<?= addslashes($admin->email) ?>',
                role: '<?= addslashes($admin->role) ?>'
            }));
            
            // Redirect to admin dashboard
            window.location.href = '/admin';
        }
    </script>
</body>
</html>
