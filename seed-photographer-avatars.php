<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Photographer;
use Illuminate\Support\Facades\Storage;

echo "=== ADDING DEFAULT PROFILE PICTURES ===\n\n";

// Create a simple placeholder SVG avatar
$placeholderSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#8B5CF6;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#7C3AED;stop-opacity:1" />
    </linearGradient>
  </defs>
  <rect width="200" height="200" fill="url(#grad)"/>
  <circle cx="100" cy="70" r="35" fill="white" opacity="0.9"/>
  <ellipse cx="100" cy="150" rx="50" ry="30" fill="white" opacity="0.9"/>
</svg>';

// Ensure avatars directory exists
$avatarsPath = storage_path('app/public/avatars');
if (!file_exists($avatarsPath)) {
    mkdir($avatarsPath, 0755, true);
    echo "Created avatars directory\n";
}

// Get photographers without profile pictures
$photographers = Photographer::whereNull('profile_picture')->limit(10)->get();

echo "Found {$photographers->count()} photographers without profile pictures\n\n";

$colors = [
    ['#8B5CF6', '#7C3AED'], // Purple
    ['#EC4899', '#DB2777'], // Pink
    ['#3B82F6', '#2563EB'], // Blue
    ['#10B981', '#059669'], // Green
    ['#F59E0B', '#D97706'], // Amber
    ['#EF4444', '#DC2626'], // Red
    ['#6366F1', '#4F46E5'], // Indigo
    ['#14B8A6', '#0D9488'], // Teal
];

foreach ($photographers as $index => $photographer) {
    $color = $colors[$index % count($colors)];
    
    // Create colored SVG
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
      <defs>
        <linearGradient id="grad' . $photographer->id . '" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" style="stop-color:' . $color[0] . ';stop-opacity:1" />
          <stop offset="100%" style="stop-color:' . $color[1] . ';stop-opacity:1" />
        </linearGradient>
      </defs>
      <rect width="200" height="200" fill="url(#grad' . $photographer->id . ')"/>
      <circle cx="100" cy="70" r="35" fill="white" opacity="0.9"/>
      <ellipse cx="100" cy="150" rx="50" ry="30" fill="white" opacity="0.9"/>
    </svg>';
    
    // Save as PNG would require GD or ImageMagick, so let's use SVG
    $filename = 'photographer-' . $photographer->id . '.svg';
    $filePath = $avatarsPath . '/' . $filename;
    
    file_put_contents($filePath, $svg);
    
    // Update photographer record
    $photographer->update([
        'profile_picture' => 'avatars/' . $filename
    ]);
    
    echo "✓ Added profile picture for {$photographer->user->name} ({$photographer->id})\n";
}

echo "\n=== DONE ===\n";
echo "Profile pictures added for {$photographers->count()} photographers\n";
