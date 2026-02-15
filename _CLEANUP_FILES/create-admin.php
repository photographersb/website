<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$user = \App\Models\User::firstOrCreate(
    ['email' => 'admin@test.com'],
    [
        'name' => 'Test Admin',
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]
);

$token = $user->createToken('admin-test')->plainTextToken;

echo "Admin User Created/Updated:\n";
echo "Email: " . $user->email . "\n";
echo "Role: " . $user->role . "\n";
echo "ID: " . $user->id . "\n\n";
echo "Auth Token:\n";
echo $token . "\n\n";
echo "Login Instructions:\n";
echo "1. Use email: admin@test.com\n";
echo "2. Use password: password123\n";
echo "3. Or use token in header: Authorization: Bearer " . $token . "\n";
