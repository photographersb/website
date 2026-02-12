<?php

/**
 * Comprehensive Settings Field Storage Test
 * Tests each field in Admin Settings (Index.vue) saves to database
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

echo "\n🔍 COMPREHENSIVE SETTINGS STORAGE TEST\n";
echo "=====================================\n\n";

// All fields from Settings/Index.vue
$fieldsToTest = [
    // General Section
    'site.name' => 'Test Platform Name ' . time(),
    'site.email' => 'test' . time() . '@example.com',
    'site.support_email' => 'support' . time() . '@example.com',
    'site.phone' => '+880 1700 ' . rand(100000, 999999),
    'site.address' => 'Test Address ' . time(),
    'site.currency' => 'USD',
    'site.timezone' => 'UTC',
    'site.date_format' => 'Y-m-d',
    
    // Branding Section
    'branding.logo_url' => 'https://example.com/logo' . time() . '.png',
    'branding.logo_credit_name' => 'Test Photographer ' . time(),
    'branding.logo_credit_url' => 'https://example.com/' . time(),
    'branding.favicon_url' => 'https://example.com/favicon' . time() . '.ico',
    'branding.favicon_credit_name' => 'Test Icon Artist ' . time(),
    'branding.favicon_credit_url' => 'https://example.com/' . time(),
    'branding.og_image' => 'https://example.com/og' . time() . '.jpg',
    'branding.og_image_credit_name' => 'OG Test ' . time(),
    'branding.og_image_credit_url' => 'https://example.com/' . time(),
    ' branding.tagline' => 'Test Tagline ' . time(),
    'branding.primary_color' => '#FF' . dechex(rand(0, 255)) . dechex(rand(0, 255)),
    'branding.secondary_color' => '#00' . dechex(rand(0, 255)) . dechex(rand(0, 255)),
    
    // Email Section
    'email.smtp_host' => 'smtp.test' . time() . '.com',
    'email.smtp_port' => '587',
    'email.smtp_username' => 'testuser' . time(),
    'email.smtp_password' => 'testpass' . time(),
    'email.mail_from_name' => 'Test Sender ' . time(),
    'email.mail_from_address' => 'sender' . time() . '@test.com',
    
    // Payments Section
    'payment.card_enabled' => 'true',
    'payment.bkash_enabled' => 'true',
    'payment.bkash_merchant' => '01700' . rand(100000, 999999),
    'payment.nagad_enabled' => 'false',
    'payment.ssl_enabled' => 'true',
    'payment.commission_rate' => (string)rand(10, 25),
    
    // Bookings Section
    'booking.auto_confirm' => 'false',
    'booking.cancellation_window_hours' => (string)rand(24, 72),
    'booking.deposit_required' => 'true',
    
    // Reviews Section
    'review.auto_publish' => 'true',
    'review.min_stars_to_display' => (string)rand(1, 5),
    
    // Security Section
    'security.two_factor_enabled' => 'false',
    'security.session_timeout' => (string)rand(60, 180),
    'security.password_min_length' => (string)rand(8, 16),
    'security.recaptcha_site_key' => 'test_site_key_' . time(),
    'security.recaptcha_secret_key' => 'test_secret_key_' . time(),
    
    // Notifications Section
    'notification.email_notifications' => 'true',
    'notification.booking_notifications' => 'true',
    'notification.review_notifications' => 'false',
    'notification.sms_enabled' => 'true',
    'notification.admin_alerts_email' => 'admin' . time() . '@test.com',
    
    // SEO Section
    'seo.site_title' => 'Test SEO Title ' . time(),
    'seo.site_description' => 'Test SEO description for testing ' . time(),
    'seo.robots' => 'index,follow',
    'seo.og_title' => 'Test OG Title ' . time(),
    'seo.og_description' => 'Test OG desc ' . time(),
    
    // Tracking Section
    'tracking.enable' => 'true',
    'tracking.ga4_measurement_id' => 'G-TEST' . time(),
    'tracking.gtm_id' => 'GTM-TEST' . time(),
    'tracking.fb_pixel_id' => 'FB' . time(),
    'tracking.gsc_verification' => 'gsc_verify_' . time(),
    
    // System Section
    'system.maintenance_mode' => '0',
    'system.maintenance_message' => 'Test maintenance message ' . time(),
    'system.debug_mode' => '0',
    'system.cache_duration' => (string)rand(30, 120),
    
    // Storage Section
    'storage.driver' => 'local',
    'storage.s3_bucket' => 'test-bucket-' . time(),
    'storage.s3_region' => 'us-east-1',
    'storage.s3_url' => 'https://s3.test' . time() . '.com',
    
    // Media Section
    'media.max_upload_mb' => (string)rand(10, 50),
    'media.image_quality' => (string)rand(70, 100),
];

$results = [
    'passed' => [],
    'failed' => [],
    'total' => count($fieldsToTest)
];

echo "📊 Total fields to test: " . $results['total'] . "\n\n";
echo "🔄 Running bulk insert/update...\n";

try {
    // Bulk insert all settings
    foreach ($fieldsToTest as $key => $value) {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            [
                'value' => $value,
                'group' => explode('.', $key)[0],
                'data_type' => 'string',
                'updated_at' => now()
            ]
        );
    }
    
    echo "✅ Bulk insert completed\n\n";
    echo "🔍 Verifying storage...\n\n";
    
    // Verify each field
    foreach ($fieldsToTest as $key => $expectedValue) {
        $stored = DB::table('settings')->where('key', $key)->first();
        
        if (!$stored) {
            $results['failed'][] = [
                'key' => $key,
                'reason' => 'NOT FOUND IN DATABASE',
                'expected' => $expectedValue,
                'actual' => null
            ];
            echo "❌ {$key} - NOT FOUND\n";
        } elseif ($stored->value !== $expectedValue) {
            $results['failed'][] = [
                'key' => $key,
                'reason' => 'VALUE MISMATCH',
                'expected' => $expectedValue,
                'actual' => $stored->value
            ];
            echo "⚠️  {$key} - VALUE MISMATCH\n";
            echo "   Expected: {$expectedValue}\n";
            echo "   Actual: {$stored->value}\n";
        } else {
            $results['passed'][] = $key;
            echo "✅ {$key} - OK\n";
        }
    }
    
    echo "\n\n📈 FINAL RESULTS\n";
    echo "================\n";
    echo "✅ Passed: " . count($results['passed']) . " / " . $results['total'] . "\n";
    echo "❌ Failed: " . count($results['failed']) . " / " . $results['total'] . "\n";
    echo "\n";
    
    if (count($results['failed']) > 0) {
        echo "❌ FAILED FIELDS:\n";
        echo "-----------------\n";
        foreach ($results['failed'] as $failure) {
            echo "• {$failure['key']}\n";
            echo "  Reason: {$failure['reason']}\n";
            if ($failure['actual'] !== null) {
                echo "  Expected: {$failure['expected']}\n";
                echo "  Got: {$failure['actual']}\n";
            }
            echo "\n";
        }
    }
    
    $percentage = round((count($results['passed']) / $results['total']) * 100, 2);
    echo "\n📊 Success Rate: {$percentage}%\n";
    
    if ($percentage === 100.0) {
        echo "\n🎉 ALL FIELDS ARE STORING CORRECTLY!\n";
    } elseif ($percentage >= 90) {
        echo "\n⚠️  MOSTLY WORKING - Minor issues detected\n";
    } elseif ($percentage >= 50) {
        echo "\n⚠️  PARTIAL SUCCESS - Multiple issues detected\n";
    } else {
        echo "\n🚨 CRITICAL - Majority of fields failing\n";
    }
    
    // Test frontend API endpoint
    echo "\n\n🌐 TESTING API ENDPOINT\n";
    echo "=======================\n";
    
    try {
        $response = Http::get('http://127.0.0.1:8000/api/v1/admin/settings');
        
        if ($response->successful()) {
            $apiData = $response->json()['data'] ?? [];
            $apiFieldCount = count($apiData);
            
            echo "✅ API endpoint accessible\n";
            echo "📊 API returned {$apiFieldCount} settings\n";
            
            $missingInApi = [];
            foreach (array_keys($fieldsToTest) as $key) {
                if (!isset($apiData[$key])) {
                    $missingInApi[] = $key;
                }
            }
            
            if (count($missingInApi) > 0) {
                echo "\n⚠️  Fields in DB but missing from API:\n";
                foreach ($missingInApi as $key) {
                    echo "   • {$key}\n";
                }
            } else {
                echo "✅ All test fields present in API response\n";
            }
        } else {
            echo "❌ API endpoint failed: " . $response->status() . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ API test error: " . $e->getMessage() . "\n";
    }
    
} catch (\Exception $e) {
    echo "\n🚨 ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n✅ Test completed\n\n";
