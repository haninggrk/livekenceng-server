<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopeeAccount;
use App\Models\Member;
use App\Services\ShopeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopeeAccountController extends Controller
{
    /**
     * Get all Shopee accounts with member info
     */
    public function index()
    {
        $shopeeAccounts = ShopeeAccount::with('member')->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'shopee_accounts' => $shopeeAccounts
        ]);
    }

    /**
     * Get a single Shopee account
     */
    public function show(ShopeeAccount $shopeeAccount)
    {
        return response()->json([
            'success' => true,
            'shopee_account' => $shopeeAccount->load('member')
        ]);
    }

    /**
     * Get Shopee accounts for a specific member
     */
    public function getByMember(Member $member)
    {
        $shopeeAccounts = $member->shopeeAccounts()->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'member' => $member,
            'shopee_accounts' => $shopeeAccounts
        ]);
    }

    /**
     * Create new Shopee account
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'name' => 'required|string|max:255',
            'cookie' => 'required|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $shopeeAccount = ShopeeAccount::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Shopee account created successfully',
            'shopee_account' => $shopeeAccount->load('member')
        ]);
    }

    /**
     * Update Shopee account
     */
    public function update(Request $request, ShopeeAccount $shopeeAccount)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'cookie' => 'required|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $shopeeAccount->update($request->only(['name', 'cookie', 'is_active']));

        return response()->json([
            'success' => true,
            'message' => 'Shopee account updated successfully',
            'shopee_account' => $shopeeAccount->load('member')
        ]);
    }

    /**
     * Delete Shopee account
     */
    public function destroy(ShopeeAccount $shopeeAccount)
    {
        $shopeeAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shopee account deleted successfully'
        ]);
    }

    /**
     * Update member's Telegram username
     */
    public function updateTelegram(Request $request, Member $member)
    {
        $validator = Validator::make($request->all(), [
            'telegram_username' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $member->update($request->only(['telegram_username']));

        return response()->json([
            'success' => true,
            'message' => 'Telegram username updated successfully',
            'member' => $member
        ]);
    }

    /**
     * Get active session for a Shopee account
     */
    public function getActiveSession(ShopeeAccount $shopeeAccount, ShopeeService $shopeeService)
    {
        if (!$shopeeAccount->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Shopee account is inactive'
            ], 400);
        }

        $sessionData = $shopeeService->getActiveSessionData($shopeeAccount->cookie);

        if (!$sessionData) {
            return response()->json([
                'success' => true,
                'session_id' => null,
                'gmv' => 0
            ]);
        }

        return response()->json([
            'success' => true,
            'session_id' => $sessionData['session_id'],
            'gmv' => $sessionData['gmv'],
            'views' => $sessionData['views'],
            'likes' => $sessionData['likes'],
            'comments' => $sessionData['comments'],
            'atc' => $sessionData['atc'],
            'placed_orders' => $sessionData['placed_orders'],
            'confirmed_orders' => $sessionData['confirmed_orders']
        ]);
    }
}
