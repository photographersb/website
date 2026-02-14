<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Api\Admin\AdminReviewController;
use Illuminate\Http\Request;

$controller = new AdminReviewController();
$request = Request::create('/api/v1/admin/reviews?search=test', 'GET', ['search' => 'test']);
$response = $controller->index($request);
$data = json_decode($response->getContent(), true);

echo "Has stats: " . (isset($data['stats']) ? 'YES' : 'NO') . "\n";
if (isset($data['stats'])) {
    print_r($data['stats']);
} else {
    echo "Full response:\n";
    print_r($data);
}
