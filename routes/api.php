<?php

use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\UpdateController;
use App\Http\Controllers\Api\ShopeeAccountController;
use App\Http\Controllers\Api\NicheController;
use App\Http\Controllers\Api\ProductSetController;
use App\Http\Controllers\Api\ShopeeLiveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Member authentication and profile routes (for desktop app)
Route::prefix('members')->group(function () {
    // Authentication
    Route::post('/login', [MemberController::class, 'login']);
    Route::post('/redeem-license', [MemberController::class, 'redeemLicense']);
    Route::post('/change-password', [MemberController::class, 'changePassword']);
    
    // Profile management
    Route::post('/profile', [MemberController::class, 'getProfile']);
    Route::put('/telegram', [MemberController::class, 'updateTelegram']);
    
    // Settings
    Route::post('/settings', [MemberController::class, 'getSettings']);
    Route::put('/settings', [MemberController::class, 'updateSettings']);
    
    // Machine ID management
    Route::get('/machine-id/{email}', [MemberController::class, 'getMachineId']);
    Route::post('/machine-id', [MemberController::class, 'updateMachineId']);
    
    // Shopee accounts
    Route::get('/shopee-accounts', [ShopeeAccountController::class, 'getMemberShopeeAccounts']);
    Route::post('/shopee-accounts', [ShopeeAccountController::class, 'addShopeeAccount']);
    Route::put('/shopee-accounts/{shopeeAccount}', [ShopeeAccountController::class, 'updateShopeeAccount']);
    Route::delete('/shopee-accounts/{shopeeAccount}', [ShopeeAccountController::class, 'deleteShopeeAccount']);
    
    // Niches
    Route::get('/niches', [NicheController::class, 'index']);
    Route::post('/niches', [NicheController::class, 'store']);
    Route::get('/niches/{niche}', [NicheController::class, 'show']);
    Route::put('/niches/{niche}', [NicheController::class, 'update']);
    Route::delete('/niches/{niche}', [NicheController::class, 'destroy']);
    
    // Product Sets
    Route::get('/product-sets', [ProductSetController::class, 'index']);
    Route::post('/product-sets', [ProductSetController::class, 'store']);
    Route::get('/product-sets/{productSet}', [ProductSetController::class, 'show']);
    Route::put('/product-sets/{productSet}', [ProductSetController::class, 'update']);
    Route::delete('/product-sets/{productSet}', [ProductSetController::class, 'destroy']);
    
    // Product Set Items
    Route::post('/product-sets/{productSet}/items', [ProductSetController::class, 'addItems']);
    Route::delete('/product-sets/{productSet}/items/{item}', [ProductSetController::class, 'removeItem']);
    Route::delete('/product-sets/{productSet}/items', [ProductSetController::class, 'clearItems']);
});

// Software update routes
Route::prefix('updates')->group(function () {
    Route::get('/{target}', [UpdateController::class, 'getUpdateInfo']);
    Route::get('/{target}/{current_version}', [UpdateController::class, 'checkUpdate']);
});

// Shopee accounts and Telegram management routes
Route::prefix('shopee')->group(function () {
    Route::post('/get-accounts', [ShopeeAccountController::class, 'getMemberShopeeAccounts']);
    Route::post('/add-account', [ShopeeAccountController::class, 'addShopeeAccount']);
    Route::put('/update-account/{shopeeAccount}', [ShopeeAccountController::class, 'updateShopeeAccount']);
    Route::delete('/delete-account/{shopeeAccount}', [ShopeeAccountController::class, 'deleteShopeeAccount']);
    Route::post('/update-telegram', [ShopeeAccountController::class, 'updateTelegram']);
    Route::get('/eligible-cookies', [ShopeeAccountController::class, 'getEligibleCookies']);
});

// Shopee Live Stream routes
Route::prefix('shopee-live')->group(function () {
    Route::post('/session-ids', [ShopeeLiveController::class, 'getSessionIds']);
    Route::post('/replace-products', [ShopeeLiveController::class, 'replaceProducts']);
    Route::post('/clear-products', [ShopeeLiveController::class, 'clearProducts']);
});
