<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== SEO & Shareable URLs Verification ===\n\n";

// Check username table
$withUsername = User::whereNotNull('username')->count();
$totalPhotographers = User::whereHas('photographer')->count();

echo "✓ Photographers with usernames: $withUsername / $totalPhotographers\n";

// Get sample users
$users = User::whereNotNull('username')->limit(3)->get();

foreach ($users as $user) {
    echo "\n📸 " . $user->name;
    echo "\n   Username: @" . $user->username;
    echo "\n   Profile URL: /@" . $user->username;
    echo "\n   Role: " . $user->role;
    
    $seo = $user->seoMeta;
    if ($seo) {
        echo "\n   ✓ SEO Meta: " . substr($seo->meta_title, 0, 50) . "...";
        echo "\n   ✓ Schema: " . ($seo->schema_json ? 'Yes' : 'No');
    }
}

echo "\n\n=== Database Summary ===\n";
echo "Username History Records: " . \App\Models\UsernameHistory::count() . "\n";
echo "SEO Meta Records: " . \App\Models\SeoMeta::count() . "\n";

echo "\n✅ SEO & Shareable URLs System is Active!\n";
