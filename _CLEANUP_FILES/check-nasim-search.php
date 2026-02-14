<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$searchTerm = 'Nasim Newaz';

echo "🔍 Searching for: {$searchTerm}\n\n";

// Check users table
$users = DB::table('users')
    ->where('name', 'like', '%Nasim%')
    ->orWhere('name', 'like', '%nasim%')
    ->get(['id', 'name', 'email', 'username']);

echo "Users found: " . count($users) . "\n";
foreach ($users as $user) {
    echo "  - ID {$user->id}: {$user->name} (@{$user->username}) - {$user->email}\n";
}

echo "\n";

// Check which ones are photographers
$photographers = DB::table('photographers')
    ->join('users', 'photographers.user_id', '=', 'users.id')
    ->where('users.name', 'like', '%Nasim%')
    ->get(['photographers.id', 'users.name', 'photographers.is_verified']);

echo "Photographers found: " . count($photographers) . "\n";
foreach ($photographers as $p) {
    echo "  - ID {$p->id}: {$p->name} (Verified: " . ($p->is_verified ? 'Yes' : 'No') . ")\n";
}
