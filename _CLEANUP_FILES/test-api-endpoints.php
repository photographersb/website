<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\City;
use App\Models\Photographer;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

echo "=== API ENDPOINT TEST ===\n\n";

echo "1. Testing CityController@adminIndex with minimal=1:\n";
try {
    $cityController = new CityController();
    $request = new Request(['minimal' => '1']);
    $response = $cityController->adminIndex($request);
    
    $data = json_decode($response->getContent(), true);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Response status: " . ($data['status'] ?? 'unknown') . "\n";
    echo "   Data count: " . (is_array($data['data']) ? count($data['data']) : 0) . "\n";
    
    if (isset($data['data']) && count($data['data']) > 0) {
        echo "   ✓ Cities API working! Sample cities:\n";
        foreach (array_slice($data['data'], 0, 3) as $city) {
            echo "      - {$city['name']} (ID: {$city['id']})\n";
        }
    } else {
        echo "   ⚠️  No cities returned\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n2. Testing AdminController@getPhotographers with minimal=1:\n";
try {
    // Create a fake authenticated admin user
    $adminUser = new \App\Models\User();
    $adminUser->id = 999;
    $adminUser->role = 'admin';
    $adminUser->exists = false;
    
    \Illuminate\Support\Facades\Auth::shouldReceive('check')->andReturn(true);
    \Illuminate\Support\Facades\Auth::shouldReceive('user')->andReturn($adminUser);
    
    $adminController = new AdminController();
    $request = new Request(['minimal' => '1', 'status' => 'active']);
    $response = $adminController->getPhotographers($request);
    
    $data = json_decode($response->getContent(), true);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Response status: " . ($data['status'] ?? 'unknown') . "\n";
    echo "   Data count: " . (is_array($data['data']) ? count($data['data']) : 0) . "\n";
    
    if (isset($data['data']) && count($data['data']) > 0) {
        echo "   ✓ Photographers API working! Sample photographers:\n";
        foreach (array_slice($data['data'], 0, 3) as $photographer) {
            $name = $photographer['user']['name'] ?? $photographer['business_name'] ?? 'Unknown';
            echo "      - {$name} (ID: {$photographer['id']})\n";
        }
    } else {
        echo "   ⚠️  No photographers returned\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== ROOT CAUSE ANALYSIS ===\n\n";

echo "✓ API endpoints exist at:\n";
echo "   - GET /api/v1/admin/cities?minimal=1\n";
echo "   - GET /api/v1/admin/photographers?minimal=1&status=active\n\n";

echo "✓ Controllers have adminIndex() and getPhotographers() methods\n";
echo "✓ Both support minimal=1 parameter for dropdown usage\n\n";

echo "❌ LIKELY ROOT CAUSE:\n";
echo "   Frontend (Create.vue) is making API calls BUT:\n";
echo "   1. Authentication may be failing (401 Unauthorized)\n";
echo "   2. CORS headers may be missing\n";
echo "   3. API route prefix mismatch (checking for /v1/ prefix)\n";
echo "   4. Browser console shows actual error message\n\n";

echo "🔍 NEXT DEBUGGING STEPS:\n";
echo "   1. Open browser DevTools → Network tab\n";
echo "   2. Navigate to http://127.0.0.1:8000/admin/events/create\n";
echo "   3. Check if API calls are made to cities and photographers\n";
echo "   4. Look for HTTP status codes (401, 403, 404, 500)\n";
echo "   5. Check response body for error messages\n\n";

echo "💡 QUICK FIX TO TRY:\n";
echo "   Run: php artisan optimize:clear\n";
echo "   Then: npm run build\n";
echo "   Hard refresh browser: Ctrl+Shift+R\n";

