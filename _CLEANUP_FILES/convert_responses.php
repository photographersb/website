<?php
/**
 * Script to convert all response()->json() calls to ApiResponse trait methods
 */

$baseDir = __DIR__ . '/app/Http/Controllers/Api';
$files = [
    'PhotographerController.php',
    'EventPaymentController.php',
    'PhotographerAwardController.php',
    'SocialAuthController.php',
    'QuoteController.php',
    'ReviewReplyController.php',
    'PhotographerCompetitionController.php',
    'PaymentController.php',
    'PortfolioController.php',
    'NotificationPreferenceController.php',
    'AwardController.php',
    'NotificationTestController.php',
    'HealthController.php',
];

$stats = [];

foreach ($files as $filename) {
    $filepath = $baseDir . '/' . $filename;
    if (!file_exists($filepath)) {
        continue;
    }

    $content = file_get_contents($filepath);
    $originalContent = $content;
    
    // Check if ApiResponse trait is already used
    if (strpos($content, 'use ApiResponse;') === false && strpos($content, 'use App\\Http\\Traits\\ApiResponse;') === false) {
        // Add trait import
        $content = preg_replace(
            '/(class \w+ extends Controller)\n\{/',
            "$1\n{\n    use ApiResponse;",
            $content
        );
    }

    // Count conversions
    $count = substr_count($originalContent, 'response()->json(');
    if ($count > 0) {
        $stats[$filename] = $count;
    }

    if ($content !== $originalContent) {
        file_put_contents($filepath, $content);
    }
}

echo "Analyzed files:\n";
$total = 0;
foreach ($stats as $file => $count) {
    echo "  $file: $count responses\n";
    $total += $count;
}
echo "\nTotal responses found: $total\n";
?>
