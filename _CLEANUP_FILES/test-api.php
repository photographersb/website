<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

echo "=== TESTING API ENDPOINTS ===\n\n";

// Test 1: All photographers
echo "1. GET /api/v1/photographers (all verified)\n";
$request = \Illuminate\Http\Request::create('/api/v1/photographers?per_page=10', 'GET');
$response = $kernel->handle($request);
$data = json_decode($response->getContent(), true);
echo "Status: {$response->getStatusCode()}\n";
echo "Total photographers: " . ($data['meta']['total'] ?? $data['total'] ?? 'unknown') . "\n";
echo "Returned: " . count($data['data'] ?? []) . "\n\n";

// Test 2: Filter by category
echo "2. GET /api/v1/photographers?category=wedding-photography\n";
$request = \Illuminate\Http\Request::create('/api/v1/photographers?category=wedding-photography&per_page=10', 'GET');
$response = $kernel->handle($request);
$data = json_decode($response->getContent(), true);
echo "Status: {$response->getStatusCode()}\n";
echo "Total with category: " . ($data['meta']['total'] ?? $data['total'] ?? 'unknown') . "\n";
echo "Returned: " . count($data['data'] ?? []) . "\n";
if (!empty($data['data'])) {
    echo "First result: {$data['data'][0]['slug']}\n";
    echo "  Categories: " . json_encode(array_column($data['data'][0]['categories'] ?? [], 'name')) . "\n";
}
echo "\n";

// Test 3: Filter by city
echo "3. GET /api/v1/photographers?city=dhaka\n";
$request = \Illuminate\Http\Request::create('/api/v1/photographers?city=dhaka&per_page=10', 'GET');
$response = $kernel->handle($request);
$data = json_decode($response->getContent(), true);
echo "Status: {$response->getStatusCode()}\n";
echo "Total in city: " . ($data['meta']['total'] ?? $data['total'] ?? 'unknown') . "\n";
echo "Returned: " . count($data['data'] ?? []) . "\n";
if (!empty($data['data'])) {
    echo "First result: {$data['data'][0]['slug']}\n";
    echo "  City: " . ($data['data'][0]['city']['name'] ?? 'NULL') . "\n";
}
echo "\n";

// Test 4: Non-existent category
echo "4. GET /api/v1/photographers?category=non-existent-category\n";
$request = \Illuminate\Http\Request::create('/api/v1/photographers?category=non-existent-category&per_page=10', 'GET');
$response = $kernel->handle($request);
$data = json_decode($response->getContent(), true);
echo "Status: {$response->getStatusCode()}\n";
echo "Total: " . ($data['meta']['total'] ?? $data['total'] ?? 'unknown') . "\n\n";

$kernel->terminate($request, $response);

echo "Done!\n";
