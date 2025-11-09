<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Cache;

echo "Testing basic cache operations...\n\n";

// Test 1: Simple cache
echo "Test 1: Basic cache set/get\n";
Cache::put('test_key', 'test_value', 60);
$value = Cache::get('test_key');

if ($value === 'test_value') {
    echo "✅ Basic cache working\n\n";
} else {
    echo "❌ Cache not working! Got: " . var_export($value, true) . "\n\n";
}

// Test 2: Cache with array
echo "Test 2: Cache with array\n";
$testData = [
    'password_hash' => 'hash123',
    'profile' => ['id' => 1, 'email' => 'test@example.com']
];

Cache::put('test_array', $testData, 60);
$retrieved = Cache::get('test_array');

if ($retrieved && isset($retrieved['password_hash']) && isset($retrieved['profile'])) {
    echo "✅ Array caching working\n";
    echo "   Password hash: " . $retrieved['password_hash'] . "\n";
    echo "   Profile email: " . $retrieved['profile']['email'] . "\n\n";
} else {
    echo "❌ Array caching failed!\n";
    var_dump($retrieved);
    echo "\n";
}

// Test 3: Check if cache persists
echo "Test 3: Cache persistence check\n";
$key = 'persist_test_' . time();
Cache::put($key, 'persistent_value', 3600);

$exists = Cache::has($key);
$value2 = Cache::get($key);

if ($exists && $value2 === 'persistent_value') {
    echo "✅ Cache persists correctly\n";
    echo "   Key: {$key}\n";
    echo "   Value: {$value2}\n\n";
} else {
    echo "❌ Cache doesn't persist\n\n";
}

// Clean up
Cache::forget('test_key');
Cache::forget('test_array');
Cache::forget($key);

echo "Basic cache test complete!\n";

