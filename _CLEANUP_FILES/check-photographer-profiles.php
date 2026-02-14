<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PHOTOGRAPHER PROFILE PICTURE CHECK ===\n\n";

$photographers = \App\Models\Photographer::where('is_verified', true)
    ->with('user')
    ->limit(10)
    ->get(['id', 'user_id', 'profile_picture', 'slug']);

echo "Found " . $photographers->count() . " verified photographers\n\n";

foreach ($photographers as $p) {
    echo "ID: {$p->id}\n";
    echo "Name: " . ($p->user->name ?? 'N/A') . "\n";
    echo "Slug: {$p->slug}\n";
    echo "Profile Picture (Raw): " . ($p->getRawOriginal('profile_picture') ?? 'NULL') . "\n";
    echo "Profile Picture (Accessor): " . ($p->profile_picture ?? 'NULL') . "\n";
    
    // Check if file exists
    if ($p->getRawOriginal('profile_picture')) {
        $path = storage_path('app/public/' . $p->getRawOriginal('profile_picture'));
        echo "File exists: " . (file_exists($path) ? 'YES' : 'NO') . " at {$path}\n";
    }
    
    echo "\n";
}

echo "\n=== CHECKING API RESPONSE ===\n\n";

// Simulate API index call
$query = \App\Models\Photographer::where('is_verified', true)
    ->with(['user', 'city', 'categories'])
    ->limit(2);

$photographers = $query->get();

echo "API Response includes profile_picture? \n";
foreach ($photographers as $p) {
    $array = $p->toArray();
    echo "Photographer: " . ($p->user->name ?? 'Unknown') . "\n";
    echo "  - profile_picture in array: " . (isset($array['profile_picture']) ? 'YES' : 'NO') . "\n";
    echo "  - Value: " . ($array['profile_picture'] ?? 'NULL') . "\n";
    echo "\n";
}
