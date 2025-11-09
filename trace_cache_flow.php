<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "========================================\n";
echo "TRACING CACHE FLOW\n";
echo "========================================\n\n";

// Create test member
$testEmail = 'trace_' . time() . '@example.com';
$testPassword = 'TestPassword123';

$member = Member::create([
    'email' => $testEmail,
    'password' => Hash::make($testPassword),
    'expiry_date' => now()->addDays(30),
]);

$cacheKey = "member_profile:" . strtolower(trim($testEmail));
Cache::forget($cacheKey);

echo "Test member: {$testEmail}\n";
echo "Cache key: {$cacheKey}\n\n";

// Request 1 - Should create cache
echo "========================================\n";
echo "REQUEST 1 - Creating cache\n";
echo "========================================\n\n";

$controller = new App\Http\Controllers\Api\MemberController();
$request1 = new Illuminate\Http\Request([
    'email' => $testEmail,
    'password' => $testPassword,
]);

echo "Before request:\n";
echo "  Cache exists? " . (Cache::has($cacheKey) ? 'YES' : 'NO') . "\n\n";

DB::enableQueryLog();
$response1 = $controller->getProfile($request1);
$queries1 = DB::getQueryLog();
DB::disableQueryLog();

echo "After request:\n";
echo "  Cache exists? " . (Cache::has($cacheKey) ? 'YES' : 'NO') . "\n";
echo "  Queries executed: " . count($queries1) . "\n\n";

// Request 2 - Should use cache
echo "========================================\n";
echo "REQUEST 2 - Should hit cache\n";
echo "========================================\n\n";

// Manually check cache first
echo "Manual cache check:\n";
$manualCache = Cache::get($cacheKey);
if ($manualCache) {
    echo "  ✅ Cache found manually\n";
    echo "  Has password_hash? " . (isset($manualCache['password_hash']) ? 'YES' : 'NO') . "\n";
    echo "  Has profile? " . (isset($manualCache['profile']) ? 'YES' : 'NO') . "\n";
    
    // Test password verification
    if (Hash::check($testPassword, $manualCache['password_hash'])) {
        echo "  ✅ Password verification works\n";
    } else {
        echo "  ❌ Password verification FAILED\n";
    }
} else {
    echo "  ❌ Cache NOT found manually\n";
}
echo "\n";

// Now make actual request
$request2 = new Illuminate\Http\Request([
    'email' => $testEmail,
    'password' => $testPassword,
]);

echo "Making request...\n";
DB::enableQueryLog();
$response2 = $controller->getProfile($request2);
$queries2 = DB::getQueryLog();
DB::disableQueryLog();

echo "After request:\n";
echo "  Queries executed: " . count($queries2) . "\n";

if (count($queries2) > 0) {
    echo "  ❌ PROBLEM: Still hitting database!\n\n";
    echo "  Queries:\n";
    foreach ($queries2 as $i => $q) {
        $sql = preg_replace('/\s+/', ' ', $q['query']);
        echo "    " . ($i + 1) . ". " . substr($sql, 0, 100) . "\n";
    }
    echo "\n";
    
    // The cache should have prevented this!
    echo "  Debugging why cache didn't prevent queries...\n";
    echo "  Cache key used: {$cacheKey}\n";
    echo "  Cache exists: " . (Cache::has($cacheKey) ? 'YES' : 'NO') . "\n";
    
    $cacheData = Cache::get($cacheKey);
    if ($cacheData) {
        echo "  Cache data structure looks correct\n";
        echo "  This means the getProfile() method is NOT using the cache!\n";
    } else {
        echo "  Cache data is MISSING!\n";
    }
} else {
    echo "  ✅ PERFECT! No database queries\n";
}

// Cleanup
Cache::forget($cacheKey);
$member->delete();

echo "\n========================================\n";
echo "ANALYSIS COMPLETE\n";
echo "========================================\n";

