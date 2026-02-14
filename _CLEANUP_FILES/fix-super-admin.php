<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing Super Admin approval status...\n";

$user = App\Models\User::where('email', 'mahidulislamnakib@gmail.com')->first();

if ($user) {
    $user->update([
        'approval_status' => 'approved',
        'approved_at' => now(),
        'approved_by' => null,
    ]);
    
    echo "✅ Super Admin updated!\n";
    echo "==================\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
    echo "approval_status: " . $user->approval_status . "\n";
    echo "approved_at: " . $user->approved_at . "\n";
    echo "\n✅ Super Admin can now login without restrictions!\n";
} else {
    echo "❌ Super admin not found!\n";
}
