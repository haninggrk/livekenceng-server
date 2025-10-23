<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UpdateController;
use App\Http\Controllers\Admin\ShopeeAccountController;
use App\Http\Controllers\Reseller\AuthController as ResellerAuthController;
use App\Http\Controllers\Reseller\DashboardController as ResellerDashboardController;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Fallback login route alias used by framework redirections
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

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

    // License plans management routes (AJAX)
    Route::get('/plans', [DashboardController::class, 'getPlans']);
    Route::get('/plans/{plan}', function ($id) {
        $plan = \App\Models\LicensePlan::findOrFail($id);
        return response()->json(['success' => true, 'plan' => $plan]);
    });
    Route::post('/plans', [DashboardController::class, 'createPlan']);
    Route::put('/plans/{plan}', [DashboardController::class, 'updatePlan']);
    Route::delete('/plans/{plan}', [DashboardController::class, 'deletePlan']);
    
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
    
    // Update management routes
    Route::get('/updates', [UpdateController::class, 'index'])->name('admin.updates.index');
    Route::get('/updates/create', [UpdateController::class, 'create'])->name('admin.updates.create');
    Route::post('/updates', [UpdateController::class, 'store'])->name('admin.updates.store');
    Route::get('/updates/{update}/edit', [UpdateController::class, 'edit'])->name('admin.updates.edit');
    Route::put('/updates/{update}', [UpdateController::class, 'update'])->name('admin.updates.update');
    Route::delete('/updates/{update}', [UpdateController::class, 'destroy'])->name('admin.updates.destroy');
    Route::post('/updates/{update}/toggle-active', [UpdateController::class, 'toggleActive'])->name('admin.updates.toggle-active');
    Route::get('/updates-data', [UpdateController::class, 'getUpdates'])->name('admin.updates.data');
    
    // Shopee accounts and Telegram management routes (AJAX)
    Route::get('/shopee-accounts', [ShopeeAccountController::class, 'index']);
    Route::get('/shopee-accounts/{shopeeAccount}', [ShopeeAccountController::class, 'show']);
    Route::get('/members/{member}/shopee-accounts', [ShopeeAccountController::class, 'getByMember']);
    Route::post('/shopee-accounts', [ShopeeAccountController::class, 'store']);
    Route::put('/shopee-accounts/{shopeeAccount}', [ShopeeAccountController::class, 'update']);
    Route::delete('/shopee-accounts/{shopeeAccount}', [ShopeeAccountController::class, 'destroy']);
    Route::put('/members/{member}/telegram', [ShopeeAccountController::class, 'updateTelegram']);
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
