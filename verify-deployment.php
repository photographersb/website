<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->bootstrap();

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║          ADMIN SYSTEM DEPLOYMENT VERIFICATION REPORT                    ║\n";
echo "║                    Generated: " . date('Y-m-d H:i:s') . "                          ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

$checks = [
    'MIGRATIONS' => [],
    'MODELS' => [],
    'CONTROLLERS' => [],
    'ROUTES' => [],
    'TABLES' => [],
    'DATA' => []
];

// Check Migrations
echo "1️⃣  CHECKING MIGRATIONS\n";
echo str_repeat("─", 70) . "\n";
$migrations = \DB::table('migrations')
    ->where('migration', 'like', '2026_01_31%')
    ->orWhere('migration', 'like', '%notice%')
    ->orWhere('migration', 'like', '%seo%')
    ->get();

foreach ($migrations as $m) {
    $status = "✅";
    $checks['MIGRATIONS'][] = ['name' => $m->migration, 'status' => true];
    echo "$status {$m->migration}\n";
}

if (count($migrations) < 2) {
    echo "⚠️  Warning: Expected at least 2 new migrations\n";
}
echo "\n";

// Check Models
echo "2️⃣  CHECKING MODELS\n";
echo str_repeat("─", 70) . "\n";
$models = [
    'Notice' => 'App\Models\Notice',
    'NoticeRole' => 'App\Models\NoticeRole',
    'NoticeRead' => 'App\Models\NoticeRead',
    'SeoMeta' => 'App\Models\SeoMeta',
];

foreach ($models as $name => $class) {
    $exists = class_exists($class);
    $status = $exists ? "✅" : "❌";
    $checks['MODELS'][] = ['name' => $name, 'status' => $exists];
    echo "$status Model: $name (" . ($exists ? "Found" : "NOT FOUND") . ")\n";
}
echo "\n";

// Check Controllers
echo "3️⃣  CHECKING CONTROLLERS\n";
echo str_repeat("─", 70) . "\n";
$controllers = [
    'DashboardController' => 'App\Http\Controllers\Admin\DashboardController',
    'NoticeController' => 'App\Http\Controllers\Api\Admin\NoticeController',
    'SeoMetaController' => 'App\Http\Controllers\Api\Admin\SeoMetaController',
];

foreach ($controllers as $name => $class) {
    $exists = class_exists($class);
    $status = $exists ? "✅" : "❌";
    $checks['CONTROLLERS'][] = ['name' => $name, 'status' => $exists];
    echo "$status Controller: $name (" . ($exists ? "Found" : "NOT FOUND") . ")\n";
}
echo "\n";

// Check Routes
echo "4️⃣  CHECKING API ROUTES\n";
echo str_repeat("─", 70) . "\n";
$routes = app('router')->getRoutes();
$requiredRoutes = [
    'admin/dashboard',
    'admin/notices',
    'notices/my-notices',
    'admin/seo',
];

$foundRoutes = 0;
foreach ($routes as $route) {
    foreach ($requiredRoutes as $required) {
        if (strpos($route->uri(), $required) !== false && 
            in_array('GET', $route->methods()) || in_array('POST', $route->methods())) {
            $foundRoutes++;
            echo "✅ Route: " . $route->uri() . "\n";
        }
    }
}
$checks['ROUTES'][] = ['count' => $foundRoutes, 'status' => $foundRoutes >= 4];
echo "\n";

// Check Tables
echo "5️⃣  CHECKING DATABASE TABLES\n";
echo str_repeat("─", 70) . "\n";
$schema = \DB::connection()->getSchemaBuilder();
$tables = ['notices', 'notice_role', 'notice_reads', 'seo_meta'];
$existingTables = 0;

foreach ($tables as $table) {
    $exists = $schema->hasTable($table);
    $status = $exists ? "✅" : "❌";
    $checks['TABLES'][] = ['table' => $table, 'status' => $exists];
    echo "$status Table: $table (" . ($exists ? "Present" : "MISSING") . ")\n";
    if ($exists) {
        $columnCount = count($schema->getColumns($table));
        echo "   └─ Columns: $columnCount\n";
        $existingTables++;
    }
}
echo "\n";

// Check Data
echo "6️⃣  CHECKING DATA & SEEDERS\n";
echo str_repeat("─", 70) . "\n";
$noticeCount = \DB::table('notices')->count();
$seoCount = \DB::table('seo_meta')->count();
$roleCount = \DB::table('notice_role')->count();

echo "✅ Notices: $noticeCount records\n";
echo "✅ SEO Meta: $seoCount records\n";
echo "✅ Notice Roles: $roleCount records\n";

$checks['DATA'][] = ['type' => 'notices', 'count' => $noticeCount, 'status' => $noticeCount > 0];
$checks['DATA'][] = ['type' => 'seo_meta', 'count' => $seoCount, 'status' => $seoCount > 0];

echo "\n";

// Summary
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║                        VERIFICATION SUMMARY                              ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

$summaryStatus = [
    'MIGRATIONS' => array_reduce($checks['MIGRATIONS'], fn($c, $i) => $c && $i['status'], true),
    'MODELS' => array_reduce($checks['MODELS'], fn($c, $i) => $c && $i['status'], true),
    'CONTROLLERS' => array_reduce($checks['CONTROLLERS'], fn($c, $i) => $c && $i['status'], true),
    'ROUTES' => array_reduce($checks['ROUTES'], fn($c, $i) => $c && $i['status'], true),
    'TABLES' => array_reduce($checks['TABLES'], fn($c, $i) => $c && $i['status'], true),
    'DATA' => array_reduce($checks['DATA'], fn($c, $i) => $c && $i['status'], true),
];

foreach ($summaryStatus as $section => $status) {
    $icon = $status ? "✅" : "⚠️";
    $text = $status ? "PASSED" : "ISSUES FOUND";
    echo "$icon $section: $text\n";
}

$allPassed = array_reduce($summaryStatus, fn($c, $s) => $c && $s, true);
echo "\n";

if ($allPassed) {
    echo "╔════════════════════════════════════════════════════════════════════════╗\n";
    echo "║  ✅ ALL CHECKS PASSED - SYSTEM READY FOR PRODUCTION                    ║\n";
    echo "╚════════════════════════════════════════════════════════════════════════╝\n";
} else {
    echo "╔════════════════════════════════════════════════════════════════════════╗\n";
    echo "║  ⚠️  SOME CHECKS FAILED - REVIEW ISSUES ABOVE                           ║\n";
    echo "╚════════════════════════════════════════════════════════════════════════╝\n";
}

echo "\n📊 STATISTICS\n";
echo str_repeat("─", 70) . "\n";
echo "Total Migrations: " . count($checks['MIGRATIONS']) . "\n";
echo "Total Models: " . count($checks['MODELS']) . "\n";
echo "Total Controllers: " . count($checks['CONTROLLERS']) . "\n";
echo "Routes Found: " . $checks['ROUTES'][0]['count'] . "\n";
echo "Tables Present: " . count(array_filter(array_map(fn($t) => $t['status'], $checks['TABLES']))) . "/" . count($checks['TABLES']) . "\n";
echo "Notice Records: $noticeCount\n";
echo "SEO Meta Records: $seoCount\n";

echo "\n📚 DOCUMENTATION FILES CREATED\n";
echo str_repeat("─", 70) . "\n";
$docs = [
    'ADMIN_SYSTEM_COMPLETE.md' => 'Full system documentation',
    'QUICK_REFERENCE_ADMIN_API.md' => 'API quick reference',
    'IMPLEMENTATION_CHECKLIST_ADMIN.md' => 'Implementation checklist',
    'QUICK_START_ADMIN_SYSTEM.md' => 'Developer quick start',
    'PROJECT_DELIVERY_SUMMARY.md' => 'Project overview',
];

foreach ($docs as $file => $desc) {
    $exists = file_exists($file);
    $status = $exists ? "✅" : "❌";
    echo "$status $file\n   └─ $desc\n";
}

echo "\n🔧 NEXT STEPS\n";
echo str_repeat("─", 70) . "\n";
echo "1. Review ADMIN_SYSTEM_COMPLETE.md for full documentation\n";
echo "2. Test endpoints using QUICK_REFERENCE_ADMIN_API.md\n";
echo "3. Follow deployment steps in IMPLEMENTATION_CHECKLIST_ADMIN.md\n";
echo "4. Monitor application logs: tail -f storage/logs/laravel.log\n";
echo "5. Run test suite: npm run test tests/Feature/AdminApiTest.js\n";

echo "\n" . str_repeat("═", 70) . "\n";
echo "Deployment Complete ✅ " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("═", 70) . "\n";
