<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Reseller\AuthController as ResellerAuthController;
use App\Http\Controllers\Reseller\DashboardController as ResellerDashboardController;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Admin authentication routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Protected admin routes
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Member management routes (AJAX)
    Route::get('/members', [DashboardController::class, 'getMembers']);
    Route::get('/members/{member}', function ($id) {
        $member = \App\Models\Member::findOrFail($id);
        return response()->json(['success' => true, 'member' => $member]);
    });
    Route::post('/members', [DashboardController::class, 'store']);
    Route::put('/members/{member}', [DashboardController::class, 'update']);
    Route::delete('/members/{member}', [DashboardController::class, 'destroy']);
    Route::post('/members/{member}/reset-password', [DashboardController::class, 'resetPassword']);
    
    // License management routes (AJAX)
    Route::get('/licenses', [DashboardController::class, 'getLicenses']);
    Route::post('/licenses/generate', [DashboardController::class, 'generateLicense']);
    Route::delete('/licenses/{license}', [DashboardController::class, 'deleteLicense']);
    Route::post('/licenses/update-price', [DashboardController::class, 'updateLicensePrice']);
    
    // Reseller management routes (AJAX)
    Route::get('/resellers', [DashboardController::class, 'getResellers']);
    Route::get('/resellers/{reseller}', function ($id) {
        $reseller = \App\Models\Reseller::findOrFail($id);
        return response()->json(['success' => true, 'reseller' => $reseller]);
    });
    Route::post('/resellers', [DashboardController::class, 'createReseller']);
    Route::put('/resellers/{reseller}', [DashboardController::class, 'updateReseller']);
    Route::delete('/resellers/{reseller}', [DashboardController::class, 'deleteReseller']);
    Route::post('/resellers/{reseller}/add-balance', [DashboardController::class, 'addBalance']);
});

// Reseller authentication routes
Route::prefix('reseller')->group(function () {
    Route::get('/login', [ResellerAuthController::class, 'showLoginForm'])->name('reseller.login');
    Route::post('/login', [ResellerAuthController::class, 'login'])->name('reseller.login.post');
    Route::post('/logout', [ResellerAuthController::class, 'logout'])->name('reseller.logout');
});

// Protected reseller routes
Route::prefix('reseller')->middleware('auth:reseller')->group(function () {
    Route::get('/', [ResellerDashboardController::class, 'index'])->name('reseller.dashboard');
    Route::get('/pricing', [ResellerDashboardController::class, 'getPricing']);
    Route::post('/licenses/generate', [ResellerDashboardController::class, 'generateLicense']);
    Route::get('/licenses', [ResellerDashboardController::class, 'getLicenses']);
});
