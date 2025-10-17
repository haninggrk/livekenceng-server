<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    /**
     * Member login with email, password, and machine ID
     */
    public function login(Request $request)
    {
        // Validate required fields
        if (!$request->email || !$request->password || !$request->machine_id) {
            return response()->json([
                'success' => false,
                'message' => 'Email, password, and machine_id are required'
            ], 400);
        }

        // Find member by email
        $member = Member::where('email', $request->email)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check and update expired status
        $member->checkAndUpdateExpiredStatus();

        // Verify password
        if (!Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check if subscription is expired
        if (!$member->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription expired. Please contact support to renew.'
            ], 401);
        }

        // Check machine ID if already set
        if ($member->machine_id && $member->machine_id !== $request->machine_id) {
            return response()->json([
                'success' => false,
                'message' => 'Machine ID mismatch'
            ], 401);
        }

        // Set machine ID if not set
        if (!$member->machine_id) {
            $member->machine_id = $request->machine_id;
            $member->save();
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $member->id,
                'email' => $member->email,
                'machine_id' => $member->machine_id,
                'expiry_date' => $member->expiry_date?->toIso8601String(),
            ]
        ]);
    }

    /**
     * Change member password (desktop app)
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
            'machine_id' => 'required|string',
        ]);

        $member = Member::where('email', $request->email)->first();
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Check and update expired status
        $member->checkAndUpdateExpiredStatus();

        if (!Hash::check($request->current_password, $member->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect'], 401);
        }

        // Enforce same device
        if ($member->machine_id && $member->machine_id !== $request->machine_id) {
            return response()->json(['success' => false, 'message' => 'Machine ID mismatch'], 401);
        }
        if (!$member->machine_id) {
            $member->machine_id = $request->machine_id;
        }

        $member->password = Hash::make($request->new_password);
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }

    /**
     * Get machine ID by email
     */
    public function getMachineId($email)
    {
        $member = Member::where('email', $email)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check and update expired status
        $member->checkAndUpdateExpiredStatus();

        return response()->json([
            'success' => true,
            'email' => $member->email,
            'machine_id' => $member->machine_id
        ]);
    }

    /**
     * Update machine ID
     */
    public function updateMachineId(Request $request)
    {
        if (!$request->email || !$request->machine_id) {
            return response()->json([
                'success' => false,
                'message' => 'Email and machine_id are required'
            ], 400);
        }

        $member = Member::where('email', $request->email)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check and update expired status
        $member->checkAndUpdateExpiredStatus();

        $member->machine_id = $request->machine_id;
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Machine ID updated successfully',
            'email' => $member->email,
            'machine_id' => $member->machine_id
        ]);
    }

    /**
     * Redeem license key
     */
    public function redeemLicense(Request $request)
    {
        if (!$request->email || !$request->license_key) {
            return response()->json([
                'success' => false,
                'message' => 'Email and license_key are required'
            ], 400);
        }

        // Validate license key first
        $licenseKey = \App\Models\LicenseKey::where('code', $request->license_key)->first();

        if (!$licenseKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key'
            ], 404);
        }

        if ($licenseKey->is_used) {
            return response()->json([
                'success' => false,
                'message' => 'License key already used'
            ], 400);
        }

        // Check if member exists, if not create new one
        $member = Member::where('email', $request->email)->first();
        $isNewMember = false;
        $generatedPassword = null;

        // If member exists, check and update expired status
        if ($member) {
            $member->checkAndUpdateExpiredStatus();
        }

        if (!$member) {
            // Create new member with random password
            $generatedPassword = \Illuminate\Support\Str::random(12);
            $member = Member::create([
                'email' => $request->email,
                'password' => Hash::make($generatedPassword),
                'expiry_date' => now()->addDays($licenseKey->duration_days),
            ]);
            $isNewMember = true;
        } else {
            // Calculate new expiry date for existing member
            $currentExpiry = $member->expiry_date && $member->expiry_date->isFuture() 
                ? $member->expiry_date 
                : now();

            $newExpiry = $currentExpiry->addDays($licenseKey->duration_days);
            
            // Update member
            $member->expiry_date = $newExpiry;
            $member->save();
        }

        // Mark license as used
        $licenseKey->is_used = true;
        $licenseKey->used_by = $member->id;
        $licenseKey->used_at = now();
        $licenseKey->save();

        $response = [
            'success' => true,
            'message' => $isNewMember 
                ? 'New account created and license activated successfully' 
                : 'License key redeemed successfully',
            'expiry_date' => $member->expiry_date->toIso8601String(),
            'days_added' => $licenseKey->duration_days,
            'is_new_member' => $isNewMember,
        ];

        // Include generated password for new members
        if ($isNewMember) {
            $response['password'] = $generatedPassword;
            $response['email'] = $member->email;
        }

        return response()->json($response);
    }
}
