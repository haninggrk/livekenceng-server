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
echo "DEFINITIVE CACHE TEST\n";
echo "========================================\n\n";

// Create member
$testEmail = 'definitive_' . time() . '@example.com';
$testPassword = 'TestPassword123';

$member = Member::create([
    'email' => $testEmail,
    'password' => Hash::make($testPassword),
    'expiry_date' => now()->addDays(30),
]);

$cacheKey = "member_profile:" . strtolower(trim($testEmail));
Cache::forget($cacheKey);

$controller = new App\Http\Controllers\Api\MemberController();

// REQUEST 1: Expect queries
echo "REQUEST 1 (should have DB queries):\n";
$initialQueryCount = DB::select('SELECT COUNT(*) as count FROM sqlite_master')[0]->count;

DB::flushQueryLog();
DB::enableQueryLog();

$req1 = new Illuminate\Http\Request(['email' => $testEmail, 'password' => $testPassword]);
$resp1 = $controller->getProfile($req1);

$queries1 = DB::getQueryLog();
DB::disableQueryLog();

echo "  Queries logged: " . count($queries1) . "\n";
foreach ($queries1 as $i => $q) {
    echo "  " . ($i+1) . ". " . substr($q['query'], 0, 60) . "...\n";
}
echo "\n";

// REQUEST 2: Should use cache (NO queries to members/subscriptions/apps)
echo "REQUEST 2 (should use cache - no member/subscription queries):\n";

DB::flushQueryLog();
DB::enableQueryLog();

$req2 = new Illuminate\Http\Request(['email' => $testEmail, 'password' => $testPassword]);
$resp2 = $controller->getProfile($req2);

$queries2 = DB::getQueryLog();
DB::disableQueryLog();

echo "  Queries logged: " . count($queries2) . "\n";

// Check if any queries are to members, subscriptions, or apps tables
$businessQueries = array_filter($queries2, function($q) {
    $sql = strtolower($q['query']);
    return str_contains($sql, 'members') || 
           str_contains($sql, 'subscriptions') || 
           str_contains($sql, 'apps');
});

if (count($businessQueries) > 0) {
    echo "  ❌ PROBLEM: Still querying business tables:\n";
    foreach ($businessQueries as $i => $q) {
        echo "    " . ($i+1) . ". " . substr($q['query'], 0, 80) . "...\n";
    }
} else {
    echo "  ✅ SUCCESS: No queries to members/subscriptions/apps tables!\n";
    echo "  (Cache is working correctly)\n";
}

echo "\n";

// Cleanup
Cache::forget($cacheKey);
$member->delete();

echo "========================================\n";
if (count($businessQueries) === 0) {
    echo "🎉 CACHE IS WORKING PERFECTLY!\n";
    echo "Database load is eliminated on cache hits!\n";
} else {
    echo "⚠️  Cache not preventing database queries\n";
}
echo "========================================\n";

