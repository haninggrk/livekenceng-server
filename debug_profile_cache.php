<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

echo "========================================\n";
echo "DEBUG: Profile Cache Behavior\n";
echo "========================================\n\n";

// Create test member
$testEmail = 'debug_' . time() . '@example.com';
$testPassword = 'TestPassword123';

$member = Member::create([
    'email' => $testEmail,
    'password' => Hash::make($testPassword),
    'expiry_date' => now()->addDays(30),
]);

echo "Created member: {$testEmail}\n\n";

$cacheKey = "member_profile:" . strtolower(trim($testEmail));

// Clear cache first
Cache::forget($cacheKey);
echo "Cache cleared for: {$cacheKey}\n\n";

// First request
echo "========================================\n";
echo "REQUEST 1 (Cache MISS expected)\n";
echo "========================================\n\n";

echo "Before request:\n";
echo "  Cache exists? " . (Cache::has($cacheKey) ? 'YES' : 'NO') . "\n\n";

$controller = new App\Http\Controllers\Api\MemberController();
$request1 = new Illuminate\Http\Request([
    'email' => $testEmail,
    'password' => $testPassword,
]);

$response1 = $controller->getProfile($request1);
$data1 = $response1->getData(true);

echo "After request:\n";
echo "  Success? " . ($data1['success'] ? 'YES' : 'NO') . "\n";
echo "  Cache exists? " . (Cache::has($cacheKey) ? 'YES' : 'NO') . "\n";

if (Cache::has($cacheKey)) {
    $cached = Cache::get($cacheKey);
    echo "  Cache contains password_hash? " . (isset($cached['password_hash']) ? 'YES' : 'NO') . "\n";
    echo "  Cache contains profile? " . (isset($cached['profile']) ? 'YES' : 'NO') . "\n";
    if (isset($cached['profile'])) {
        echo "  Profile email: " . ($cached['profile']['email'] ?? 'N/A') . "\n";
    }
}
echo "\n";

// Second request
echo "========================================\n";
echo "REQUEST 2 (Cache HIT expected)\n";
echo "========================================\n\n";

echo "Before request:\n";
echo "  Cache exists? " . (Cache::has($cacheKey) ? 'YES' : 'NO') . "\n\n";

$request2 = new Illuminate\Http\Request([
    'email' => $testEmail,
    'password' => $testPassword,
]);

$response2 = $controller->getProfile($request2);
$data2 = $response2->getData(true);

echo "After request:\n";
echo "  Success? " . ($data2['success'] ? 'YES' : 'NO') . "\n";
echo "  Response matches? " . ($data1['data']['email'] === $data2['data']['email'] ? 'YES' : 'NO') . "\n\n";

// Third request (to confirm)
echo "========================================\n";
echo "REQUEST 3 (Cache HIT expected)\n";
echo "========================================\n\n";

$request3 = new Illuminate\Http\Request([
    'email' => $testEmail,
    'password' => $testPassword,
]);

$response3 = $controller->getProfile($request3);
$data3 = $response3->getData(true);

echo "Success? " . ($data3['success'] ? 'YES' : 'NO') . "\n";
echo "Cache still exists? " . (Cache::has($cacheKey) ? 'YES' : 'NO') . "\n\n";

// Check cache directly
echo "========================================\n";
echo "DIRECT CACHE CHECK\n";
echo "========================================\n\n";

$directCache = Cache::get($cacheKey);
if ($directCache) {
    echo "✅ Cache data found!\n";
    echo "  Keys: " . implode(', ', array_keys($directCache)) . "\n";
    if (isset($directCache['profile'])) {
        echo "  Profile keys: " . implode(', ', array_keys($directCache['profile'])) . "\n";
    }
} else {
    echo "❌ No cache data found!\n";
}

// Cleanup
Cache::forget($cacheKey);
Cache::forget('app:livekenceng');
$member->delete();

echo "\n✅ Debug complete\n";

