<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopeeAccount;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ShopeeAccountController extends Controller
{
    /**
     * Get Shopee accounts for a member (requires authentication)
     */
    public function getMemberShopeeAccounts(Request $request)
    {
        // Support both POST (query params) and GET (request params)
        $email = $request->get('email') ?? $request->email;
        $password = $request->get('password') ?? $request->password;

        if (!$email || !$password) {
            return response()->json([
                'success' => false,
                'message' => 'Email and password are required'
            ], 400);
        }

        $member = Member::where('email', $email)->first();
        
        if (!$member || !Hash::check($password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check and update expired status
        $member->checkAndUpdateExpiredStatus();

        $shopeeAccounts = $member->shopeeAccounts()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $shopeeAccounts
        ]);
    }

    /**
     * Add Shopee account to a member
     */
    public function addShopeeAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:members,email',
            'password' => 'required',
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

        $member = Member::where('email', $request->email)->first();
        
        if (!Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $shopeeAccount = ShopeeAccount::create([
            'member_id' => $member->id,
            'name' => $request->name,
            'cookie' => $request->cookie,
            'is_active' => $request->is_active ?? true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shopee account added successfully',
            'data' => $shopeeAccount
        ]);
    }

    /**
     * Update Shopee account
     */
    public function updateShopeeAccount(Request $request, ShopeeAccount $shopeeAccount)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:members,email',
            'password' => 'required',
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

        $member = Member::where('email', $request->email)->first();
        
        if (!Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check if the Shopee account belongs to this member
        if ($shopeeAccount->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to Shopee account'
            ], 403);
        }

        $shopeeAccount->update([
            'name' => $request->name,
            'cookie' => $request->cookie,
            'is_active' => $request->is_active ?? $shopeeAccount->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shopee account updated successfully',
            'shopee_account' => $shopeeAccount
        ]);
    }

    /**
     * Delete Shopee account
     */
    public function deleteShopeeAccount(Request $request, ShopeeAccount $shopeeAccount)
    {
        if (!$request->email || !$request->password) {
            return response()->json([
                'success' => false,
                'message' => 'Email and password are required'
            ], 400);
        }

        $member = Member::where('email', $request->email)->first();
        
        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check if the Shopee account belongs to this member
        if ($shopeeAccount->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to Shopee account'
            ], 403);
        }

        $shopeeAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shopee account deleted successfully'
        ]);
    }

    /**
     * Update member's Telegram username
     */
    public function updateTelegram(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:members,email',
            'password' => 'required',
            'telegram_username' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $member = Member::where('email', $request->email)->first();
        
        if (!Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $member->update(['telegram_username' => $request->telegram_username]);

        return response()->json([
            'success' => true,
            'message' => 'Telegram username updated successfully',
            'member' => $member
        ]);
    }

    /**
     * Get eligible cookies - returns cookies and telegram for all active members
     */
    public function getEligibleCookies()
    {
        $activeMembers = Member::where('expiry_date', '>', now())
            ->whereNotNull('expiry_date')
            ->with(['activeShopeeAccounts'])
            ->get();

        $eligibleData = $activeMembers->map(function ($member) {
            return [
                'member_id' => $member->id,
                'email' => $member->email,
                'telegram_username' => $member->telegram_username,
                'shopee_accounts' => $member->activeShopeeAccounts->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'name' => $account->name,
                        'cookie' => $account->cookie
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'eligible_members' => $eligibleData,
            'total_active_members' => $activeMembers->count()
        ]);
    }
}
