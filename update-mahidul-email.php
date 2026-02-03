<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

use App\Models\User;

// Update Mahidul Islam Nakib with Gmail address
$admin = User::updateOrCreate(
    ['email' => 'mahidulislamnakib@gmail.com'],
    [
        'name' => 'Mahidul Islam Nakib',
        'email' => 'mahidulislamnakib@gmail.com',
        'phone' => '+8801711111111',
        'role' => 'admin',
        'password' => bcrypt('admin123456'),
        'email_verified_at' => now(),
        'is_active' => true,
    ]
);

// Create auth token
$token = $admin->createToken('admin-token')->plainTextToken;

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║        ✅ ADMIN EMAIL UPDATED SUCCESSFULLY                 ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "👤 Admin User Details:\n";
echo "   Name:     $admin->name\n";
echo "   Email:    $admin->email ✅ (Updated)\n";
echo "   Role:     $admin->role\n";
echo "   ID:       $admin->id\n";
echo "   Status:   Active ✅\n\n";

echo "🔐 Login Credentials:\n";
echo "   Email:    $admin->email\n";
echo "   Password: admin123456\n\n";

echo "🔑 Auth Token:\n";
echo "   " . substr($token, 0, 40) . "...\n\n";

echo "📍 Access Dashboard:\n";
echo "   Frontend: http://localhost:5173/auth\n";
echo "   Admin UI: http://localhost:5173/admin/dashboard\n";

$kernel->terminate($request, $response);
?>
