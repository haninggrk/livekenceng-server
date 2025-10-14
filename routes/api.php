<?php

use App\Http\Controllers\Api\MemberController;
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
