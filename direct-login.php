<?php
// Direct login token generator - bypasses password check
// Upload to server and run: php direct-login.php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$email = 'mahidulislamnakib@gmail.com';
$user = \App\Models\User::where('email', $email)->first();

if (!$user) {
    die("User not found\n");
}

// Generate token
$token = $user->createToken('direct-login')->plainTextToken;

echo "=============================================\n";
echo "DIRECT LOGIN TOKEN GENERATED\n";
echo "=============================================\n";
echo "User: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Role: {$user->role}\n";
echo "\nToken: {$token}\n\n";
echo "=============================================\n";
echo "MANUAL SETUP:\n";
echo "=============================================\n";
echo "1. Open browser console (F12)\n";
echo "2. Go to Application → Local Storage\n";
echo "3. Add these keys:\n\n";
echo "   Key: auth_token\n";
echo "   Value: {$token}\n\n";
echo "   Key: user\n";
echo "   Value: " . json_encode([
    'id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'role' => $user->role
]) . "\n\n";
echo "4. Refresh page - you'll be logged in\n";
echo "=============================================\n";
?>
