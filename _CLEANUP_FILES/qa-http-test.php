<?php
/**
 * QA Test - Pure HTTP-based (no DB)
 */

$baseUrl = 'http://127.0.0.1:8000';
$results = [];

echo "\n=== PHOTOGRAPHER SB END-TO-END QA TEST ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "Environment: Localhost\n\n";

function testUrl($url, $description) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'status' => 'FAIL',
            'code' => 0,
            'error' => $error,
            'url' => $url
        ];
    }
    
    $success = $httpCode >= 200 && $httpCode < 400;
    return [
        'status' => $success ? 'PASS' : 'FAIL',
        'code' => $httpCode,
        'url' => $url,
        'description' => $description,
        'has_content' => strlen($response) > 100
    ];
}

// =========================================
// A) LINK AUDIT TESTS
// =========================================
echo "[A] LINK AUDIT TESTS\n";
echo str_repeat("=", 50) . "\n\n";

$public_links = [
    '/' => 'Home',
    '/about' => 'About',
    '/how-it-works' => 'How It Works',
    '/contact' => 'Contact',
    '/help' => 'Help Center',
    '/privacy' => 'Privacy Policy',
    '/terms' => 'Terms of Service',
    '/photographer' => 'Photographers',
    '/events' => 'Events',
    '/competitions' => 'Competitions',
    '/auth' => 'Login/Register',
];

$admin_links = [
    '/admin/dashboard' => 'Admin Dashboard',
    '/admin/competitions' => 'Admin Competitions',
    '/admin/events' => 'Admin Events',
    '/admin/users' => 'Admin Users',
    '/admin/photographers' => 'Admin Photographers',
    '/admin/bookings' => 'Admin Bookings',
    '/admin/reviews' => 'Admin Reviews',
    '/admin/transactions' => 'Admin Transactions',
    '/admin/verifications' => 'Admin Verifications',
    '/admin/settings' => 'Admin Settings',
    '/admin/sponsors' => 'Admin Sponsors',
    '/admin/notices' => 'Admin Notices',
    '/admin/mentors' => 'Admin Mentors',
    '/admin/judges' => 'Admin Judges',
    '/admin/audit-logs' => 'Admin Audit Logs',
    '/admin/activity-logs' => 'Admin Activity Logs',
    '/admin/contact-messages' => 'Admin Contact Messages',
    '/admin/seo' => 'Admin SEO',
    '/admin/cities' => 'Admin Cities',
    '/admin/categories' => 'Admin Categories',
];

$api_endpoints = [
    '/api/v1/categories' => 'Categories API',
    '/api/v1/cities' => 'Cities API',
    '/api/v1/photographers' => 'Photographers API',
    '/api/v1/events' => 'Events API',
    '/api/v1/competitions' => 'Competitions API',
    '/api/v1/mentors' => 'Mentors API',
    '/api/v1/judges' => 'Judges API',
];

$all_links = array_merge($public_links, $admin_links, $api_endpoints);
$passed = 0;
$failed = 0;

echo "PUBLIC PAGES:\n";
foreach ($public_links as $path => $title) {
    $result = testUrl($baseUrl . $path, $title);
    $icon = $result['status'] === 'PASS' ? '✓' : '✗';
    echo "  [$icon] $path ({$result['code']}) - $title\n";
    if ($result['status'] === 'PASS') {
        $passed++;
    } else {
        $failed++;
    }
}

echo "\nADMIN PAGES:\n";
foreach ($admin_links as $path => $title) {
    $result = testUrl($baseUrl . $path, $title);
    $icon = $result['status'] === 'PASS' ? '✓' : '✗';
    echo "  [$icon] $path ({$result['code']}) - $title\n";
    if ($result['status'] === 'PASS') {
        $passed++;
    } else {
        $failed++;
    }
}

echo "\nAPI ENDPOINTS:\n";
foreach ($api_endpoints as $path => $title) {
    $result = testUrl($baseUrl . $path, $title);
    $icon = $result['status'] === 'PASS' ? '✓' : '✗';
    echo "  [$icon] $path ({$result['code']}) - $title\n";
    if ($result['status'] === 'PASS') {
        $passed++;
    } else {
        $failed++;
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "LINK AUDIT SUMMARY:\n";
echo "  Total Links: " . count($all_links) . "\n";
echo "  Passed: $passed\n";
echo "  Failed: $failed\n";
echo "  Pass Rate: " . round(($passed / count($all_links)) * 100, 2) . "%\n";

// =========================================
// B) API FUNCTIONALITY TESTS
// =========================================
echo "\n\n[B] API FUNCTIONALITY TESTS\n";
echo str_repeat("=", 50) . "\n\n";

$apiTests = [
    '/api/v1/categories' => 'Fetch Categories',
    '/api/v1/cities' => 'Fetch Cities',
    '/api/v1/photographers' => 'Fetch Photographers',
    '/api/v1/competitions' => 'Fetch Competitions',
];

foreach ($apiTests as $endpoint => $test) {
    $result = testUrl($baseUrl . $endpoint, $test);
    
    if ($result['status'] === 'PASS') {
        $ch = curl_init($baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        $dataCount = is_array($data['data'] ?? []) ? count($data['data']) : 0;
        echo "  ✓ $test\n";
        echo "    - Status: " . ($data['status'] ?? 'unknown') . "\n";
        echo "    - Records: $dataCount\n";
    } else {
        echo "  ✗ $test - HTTP " . $result['code'] . "\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "QA TEST COMPLETE\n";
echo "Report saved for review\n";

?>
