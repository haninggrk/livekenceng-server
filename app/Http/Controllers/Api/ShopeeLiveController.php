<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\ShopeeAccount;
use App\Services\ShopeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ShopeeLiveController extends Controller
{
    protected $shopeeService;

    public function __construct(ShopeeService $shopeeService)
    {
        $this->shopeeService = $shopeeService;
    }

    /**
     * Get live stream session IDs for a Shopee account
     */
    public function getSessionIds(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'shopee_account_id' => 'required|exists:shopee_accounts,id',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $shopeeAccount = ShopeeAccount::find($request->shopee_account_id);

        if (!$shopeeAccount || $shopeeAccount->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Shopee account not found or does not belong to you'
            ], 404);
        }

        if (!$shopeeAccount->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Shopee account is inactive'
            ], 400);
        }

        $sessionIds = $this->shopeeService->getSessionIds($shopeeAccount->cookie);

        return response()->json([
            'success' => true,
            'session_ids' => $sessionIds,
            'count' => count($sessionIds)
        ]);
    }

    /**
     * Replace products in live stream
     */
    public function replaceProducts(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'shopee_account_id' => 'required|exists:shopee_accounts,id',
            'session_id' => 'required|string',
            'product_set_id' => 'required|exists:product_sets,id',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $shopeeAccount = ShopeeAccount::find($request->shopee_account_id);

        if (!$shopeeAccount || $shopeeAccount->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Shopee account not found or does not belong to you'
            ], 404);
        }

        if (!$shopeeAccount->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Shopee account is inactive'
            ], 400);
        }

        $productSet = \App\Models\ProductSet::find($request->product_set_id);

        if (!$productSet || $productSet->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product set not found or does not belong to you'
            ], 404);
        }

        $items = $productSet->items()->get()->map(function ($item) {
            return [
                'shop_id' => $item->shop_id,
                'item_id' => $item->item_id,
            ];
        })->toArray();

        $result = $this->shopeeService->replaceProducts(
            $shopeeAccount->cookie,
            $request->session_id,
            $items
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'items_count' => count($items)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], 400);
        }
    }

    /**
     * Clear all products from live stream (pass empty array)
     */
    public function clearProducts(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'shopee_account_id' => 'required|exists:shopee_accounts,id',
            'session_id' => 'required|string',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $shopeeAccount = ShopeeAccount::find($request->shopee_account_id);

        if (!$shopeeAccount || $shopeeAccount->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Shopee account not found or does not belong to you'
            ], 404);
        }

        if (!$shopeeAccount->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Shopee account is inactive'
            ], 400);
        }

        $result = $this->shopeeService->replaceProducts(
            $shopeeAccount->cookie,
            $request->session_id,
            [] // Empty array to clear products
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Products cleared successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], 400);
        }
    }
}
