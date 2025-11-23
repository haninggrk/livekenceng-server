<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\TikTokAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TikTokAccountController extends Controller
{
    /**
     * Get TikTok accounts for a member (requires authentication)
     */
    public function getMemberTikTokAccounts(Request $request)
    {
        // Support both POST (query params) and GET (request params)
        $email = $request->get('email') ?? $request->email;
        $password = $request->get('password') ?? $request->password;

        if (! $email || ! $password) {
            return response()->json([
                'success' => false,
                'message' => 'Email and password are required',
            ], 400);
        }

        $member = Member::where('email', $email)->first();

        if (! $member || ! Hash::check($password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Check and update expired status
        $member->checkAndUpdateExpiredStatus();

        $tiktokAccounts = $member->tiktokAccounts()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $tiktokAccounts,
        ]);
    }

    /**
     * Add TikTok account to a member
     */
    public function addTikTokAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:members,email',
            'password' => 'required',
            'name' => 'required|string|max:255',
            'email_account' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'cookie' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $member = Member::where('email', $request->email)->first();

        if (! Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $tiktokAccount = TikTokAccount::create([
            'member_id' => $member->id,
            'name' => $request->name,
            'email' => $request->email_account,
            'username' => $request->username,
            'cookie' => $request->cookie,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'TikTok account added successfully',
            'data' => $tiktokAccount,
        ]);
    }

    /**
     * Update TikTok account
     */
    public function updateTikTokAccount(Request $request, TikTokAccount $tiktokAccount)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:members,email',
            'password' => 'required',
            'name' => 'required|string|max:255',
            'email_account' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'cookie' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $member = Member::where('email', $request->email)->first();

        if (! Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Check if the TikTok account belongs to this member
        if ($tiktokAccount->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to TikTok account',
            ], 403);
        }

        $tiktokAccount->update([
            'name' => $request->name,
            'email' => $request->email_account,
            'username' => $request->username,
            'cookie' => $request->cookie,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'TikTok account updated successfully',
            'data' => $tiktokAccount,
        ]);
    }

    /**
     * Delete TikTok account
     */
    public function deleteTikTokAccount(Request $request, TikTokAccount $tiktokAccount)
    {
        if (! $request->email || ! $request->password) {
            return response()->json([
                'success' => false,
                'message' => 'Email and password are required',
            ], 400);
        }

        $member = Member::where('email', $request->email)->first();

        if (! $member || ! Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Check if the TikTok account belongs to this member
        if ($tiktokAccount->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to TikTok account',
            ], 403);
        }

        $tiktokAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'TikTok account deleted successfully',
        ]);
    }
}
