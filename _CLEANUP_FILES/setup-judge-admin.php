<?php
/**
 * Setup Judge Role for Admin Users
 * - Verifies email for nasimnewaz@gmail.com
 * - Promotes admin users to have judge role
 */

define('LARAVEL_START', microtime(true));

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Judge;

echo "=== JUDGE ADMIN SETUP ===\n\n";

// 1. Verify email for nasimnewaz@gmail.com
echo "1. Verifying email for nasimnewaz@gmail.com...\n";
$user = User::where('email', 'nasimnewaz@gmail.com')->first();
if ($user) {
    $user->email_verified_at = now();
    $user->save();
    echo "   ✓ Email verified\n";
} else {
    echo "   ✗ User not found\n";
}

// 2. Auto-promote all admins and super_admins to judges
echo "\n2. Creating/promoting judges for admin users...\n";
$admins = User::whereIn('role', ['admin', 'super_admin'])->get();

foreach ($admins as $admin) {
    // Check if judge record exists
    $judge = Judge::where('user_id', $admin->id)->first();
    
    if (!$judge) {
        // Create judge record
        $judge = Judge::create([
            'user_id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'is_active' => true,
            'sort_order' => 0,
        ]);
        echo "   ✓ Created judge record for {$admin->name} ({$admin->email})\n";
    } else {
        echo "   → Judge record already exists for {$admin->name}\n";
    }
}

echo "\n=== SETUP COMPLETE ===\n";
echo "- Email verified: nasimnewaz@gmail.com\n";
echo "- Admin users promoted to judges (where not already exists)\n";
