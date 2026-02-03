<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Get admin user
$admin = \App\Models\User::find(47);
if ($admin) {
    echo "✅ Admin found:\n";
    echo "   Email: " . $admin->email . "\n";
    echo "   Role: " . $admin->role . "\n";
    echo "   ID: " . $admin->id . "\n";
    echo "   Name: " . $admin->name . "\n";
    
    // Get existing token
    $tokens = \DB::table('personal_access_tokens')
        ->where('tokenable_id', 47)
        ->where('name', 'admin-token')
        ->latest()
        ->first();
    
    if ($tokens) {
        echo "   Token (first 20 chars): " . substr($tokens->token, 0, 20) . "...\n";
        echo "\n✅ Test Admin Account is ready!\n";
    } else {
        echo "   No token found\n";
    }
} else {
    echo "❌ No admin found with ID 47\n";
}

$kernel->terminate($request, $response);
?>
