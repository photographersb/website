<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

echo "=== CHECKING API RESPONSE STRUCTURE ===\n\n";

// Test category filter
echo "GET /api/v1/photographers?category=wedding-photography&per_page=100\n";
$request = \Illuminate\Http\Request::create('/api/v1/photographers?category=wedding-photography&per_page=100', 'GET');
$response = $kernel->handle($request);
$data = json_decode($response->getContent(), true);

echo "\nFull Response Structure:\n";
echo json_encode($data, JSON_PRETTY_PRINT);

$kernel->terminate($request, $response);
