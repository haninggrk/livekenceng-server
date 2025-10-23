<?php

use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\UpdateController;
use App\Http\Controllers\Api\ShopeeAccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Member authentication routes (for desktop app)
Route::prefix('members')->group(function () {
    Route::post('/login', [MemberController::class, 'login']);
    Route::get('/machine-id/{email}', [MemberController::class, 'getMachineId']);
    Route::post('/machine-id', [MemberController::class, 'updateMachineId']);
    Route::post('/redeem-license', [MemberController::class, 'redeemLicense']);
    Route::post('/change-password', [MemberController::class, 'changePassword']);
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
