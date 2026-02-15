<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$photographers = \App\Models\Photographer::with('user')->limit(10)->get(['id', 'user_id', 'slug']);

echo "Available Photographers:\n";
echo str_repeat('-', 80) . "\n";

foreach ($photographers as $p) {
    $name = $p->user ? $p->user->name : 'Unknown';
    echo sprintf("ID: %-3d | Slug: %-30s | Name: %s\n", $p->id, $p->slug, $name);
}

echo "\nTo view profile, use: http://127.0.0.1:8000/photographer/{slug}\n";
