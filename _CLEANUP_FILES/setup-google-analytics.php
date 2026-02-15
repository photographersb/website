<?php
/**
 * Google Analytics Setup Script
 * Sets up G-PYWLWNZR5K tracking ID in the settings table
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

echo "=== Google Analytics 4 Setup ===" . PHP_EOL . PHP_EOL;

$ga4Id = 'G-PYWLWNZR5K';
$gtmId = 'GTM-T3BW6WBM';
$fbPixelId = '1588515025525995';
$fbDomainVerification = 'fnhxia9k1fpialf0r9xh5bwzf3qsde';

try {
    // Check if settings table exists
    $tableExists = DB::select("SHOW TABLES LIKE 'settings'");
    
    if (empty($tableExists)) {
        echo "❌ Settings table does not exist. Creating..." . PHP_EOL;
        
        DB::statement("CREATE TABLE IF NOT EXISTS settings (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `key` VARCHAR(191) UNIQUE NOT NULL,
            `value` TEXT NULL,
            `type` VARCHAR(50) DEFAULT 'string',
            `group` VARCHAR(100) NULL,
            `description` TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");
        
        echo "✅ Settings table created" . PHP_EOL . PHP_EOL;
    } else {
        echo "✅ Settings table exists" . PHP_EOL . PHP_EOL;
    }
    
    echo "Checking existing tracking settings..." . PHP_EOL;
    
    $trackingKeys = [
        'tracking.enable',
        'tracking.ga4_measurement_id',
        'tracking.gtm_id',
        'tracking.fb_pixel_id',
        'tracking.gsc_verification',
        'tracking.fb_domain_verification'
    ];
    
    $existing = DB::table('settings')
        ->whereIn('key', $trackingKeys)
        ->get(['key', 'value'])
        ->pluck('value', 'key')
        ->toArray();
    
    echo "Current settings:" . PHP_EOL;
    foreach ($trackingKeys as $key) {
        $value = $existing[$key] ?? '(not set)';
        echo "  - {$key}: {$value}" . PHP_EOL;
    }
    echo PHP_EOL;
    
    // Set up tracking settings
    $settings = [
        [
            'key' => 'tracking.enable',
            'value' => '1',
            'type' => 'boolean',
            'group' => 'tracking',
            'description' => 'Enable/disable all tracking scripts'
        ],
        [
            'key' => 'tracking.ga4_measurement_id',
            'value' => $ga4Id,
            'type' => 'string',
            'group' => 'tracking',
            'description' => 'Google Analytics 4 Measurement ID'
        ],
        [
            'key' => 'tracking.gtm_id',
            'value' => $gtmId,
            'type' => 'string',
            'group' => 'tracking',
            'description' => 'Google Tag Manager ID'
        ],
        [
            'key' => 'tracking.fb_pixel_id',
            'value' => $fbPixelId,
            'type' => 'string',
            'group' => 'tracking',
            'description' => 'Meta Pixel ID'
        ],
        [
            'key' => 'tracking.fb_domain_verification',
            'value' => $fbDomainVerification,
            'type' => 'string',
            'group' => 'tracking',
            'description' => 'Facebook domain verification token'
        ]
    ];
    
    echo "Updating tracking settings..." . PHP_EOL;
    
    $availableColumns = array_flip(DB::getSchemaBuilder()->getColumnListing('settings'));

    foreach ($settings as $setting) {
        $payload = [
            'key' => $setting['key'],
            'value' => $setting['value']
        ];

        if (isset($availableColumns['type'])) {
            $payload['type'] = $setting['type'];
        }

        if (isset($availableColumns['group'])) {
            $payload['group'] = $setting['group'];
        }

        if (isset($availableColumns['description'])) {
            $payload['description'] = $setting['description'];
        }

        if (isset($availableColumns['created_at'])) {
            $payload['created_at'] = now();
        }

        if (isset($availableColumns['updated_at'])) {
            $payload['updated_at'] = now();
        }

        DB::table('settings')->updateOrInsert(
            ['key' => $setting['key']],
            $payload
        );
        
        echo "✅ Set {$setting['key']} = {$setting['value']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "Clearing cache..." . PHP_EOL;
    Cache::forget('tracking_settings');
    echo "✅ Cache cleared" . PHP_EOL . PHP_EOL;
    
    // Verify final settings
    echo "=== Final Configuration ===" . PHP_EOL;
    $final = DB::table('settings')
        ->whereIn('key', $trackingKeys)
        ->get(['key', 'value'])
        ->pluck('value', 'key')
        ->toArray();
    
    foreach ($trackingKeys as $key) {
        $value = $final[$key] ?? '(not set)';
        $status = isset($final[$key]) ? '✅' : '⚠️';
        echo "{$status} {$key}: {$value}" . PHP_EOL;
    }
    
    echo PHP_EOL . "=== Summary ===" . PHP_EOL;
    echo "✅ Google Analytics 4 tracking configured with ID: {$ga4Id}" . PHP_EOL;
    echo "✅ Google Tag Manager configured with ID: {$gtmId}" . PHP_EOL;
    echo "✅ Meta Pixel configured with ID: {$fbPixelId}" . PHP_EOL;
    echo "✅ Facebook domain verification set: {$fbDomainVerification}" . PHP_EOL;
    echo "✅ Tracking enabled: YES" . PHP_EOL . PHP_EOL;
    echo "The tracking scripts will now load on all public pages." . PHP_EOL;
    echo "Visit https://photographersb.com and check page source for:" . PHP_EOL;
    echo "  - gtag('config', '{$ga4Id}')" . PHP_EOL;
    echo "  - https://www.googletagmanager.com/gtag/js?id={$ga4Id}" . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
    exit(1);
}
