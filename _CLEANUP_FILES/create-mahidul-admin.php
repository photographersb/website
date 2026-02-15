<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

use App\Models\User;

// Create or update Mahidul Islam Nakib as admin
$admin = User::updateOrCreate(
    ['email' => 'mahidul@admin.com'],
    [
        'name' => 'Mahidul Islam Nakib',
        'email' => 'mahidul@admin.com',
        'phone' => '+8801700000000',
        'role' => 'admin',
        'password' => bcrypt('admin123456'),
        'email_verified_at' => now(),
        'is_active' => true,
    ]
);

// Create auth token
$token = $admin->createToken('admin-token')->plainTextToken;

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║        ✅ ADMIN USER CREATED/UPDATED SUCCESSFULLY          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "👤 Admin User Details:\n";
echo "   Name:     $admin->name\n";
echo "   Email:    $admin->email\n";
echo "   Role:     $admin->role\n";
echo "   ID:       $admin->id\n";
echo "   Status:   Active ✅\n\n";

echo "🔐 Login Credentials:\n";
echo "   Email:    $admin->email\n";
echo "   Password: admin123456\n\n";

echo "🔑 Auth Token (First 30 chars):\n";
echo "   " . substr($token, 0, 30) . "...\n";
echo "   Full: $token\n\n";

echo "📍 Access Dashboard:\n";
echo "   Frontend: http://localhost:5173/auth\n";
echo "   Admin UI: http://localhost:5173/admin/dashboard\n";
echo "   API:      http://localhost:8000/api/v1/admin/dashboard\n\n";

$kernel->terminate($request, $response);
?>
