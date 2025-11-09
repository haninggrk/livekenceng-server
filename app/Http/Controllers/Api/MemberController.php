<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class MemberController extends Controller
{
    /**
     * Member login with email, password, and machine ID
     * Supports both multi-app (with app_identifier) and legacy single-app systems
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

        // Verify password
        if (!Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // NEW: Check if using multi-app system
        $appIdentifier = $request->input('app_identifier');
        
        if ($appIdentifier) {
            // Multi-app login flow
            $app = \App\Models\App::where('identifier', $appIdentifier)->first();
            
            if (!$app) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid app identifier'
                ], 400);
            }
            
            // Get or create subscription for this app
            $subscription = $member->getSubscriptionForApp($app->id);
            
            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No subscription found for this app'
                ], 401);
            }
            
            // Check and update expired status
            $member->checkAndUpdateExpiredStatus();
            
            // Check if subscription is expired for this app
            if (!$subscription->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription expired for this app. Please contact support to renew.'
                ], 401);
            }
            
            // Check machine ID for this app
            if ($subscription->machine_id && $subscription->machine_id !== $request->machine_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Machine ID mismatch for this app'
                ], 401);
            }
            
            // Set machine ID for this app if not set
            if (!$subscription->machine_id) {
                $subscription->machine_id = $request->machine_id;
            }
            $subscription->last_login_at = now();
            $subscription->save();
            
            $machineId = $subscription->machine_id;
            $expiryDate = $subscription->expiry_date;
            
        } else {
            // BACKWARD COMPATIBILITY: Legacy login - default to livekenceng app
            $defaultApp = \App\Models\App::where('identifier', 'livekenceng')->first();
            
            if ($defaultApp) {
                // Use livekenceng app subscription
                $subscription = $member->getSubscriptionForApp($defaultApp->id);
                
                if (!$subscription) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No subscription found for livekenceng app'
                    ], 401);
                }
                
                // Check if subscription is expired
                if (!$subscription->isActive()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Subscription expired. Please contact support to renew.'
                    ], 401);
                }
                
                // Check machine ID for this app
                if ($subscription->machine_id && $subscription->machine_id !== $request->machine_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Machine ID mismatch'
                    ], 401);
                }
                
                // Set machine ID for this app if not set
                if (!$subscription->machine_id) {
                    $subscription->machine_id = $request->machine_id;
                }
                $subscription->last_login_at = now();
                $subscription->save();
                
                $machineId = $subscription->machine_id;
                $expiryDate = $subscription->expiry_date;
                
            } else {
                // Fallback: Use old members table if no default app exists
                $member->checkAndUpdateExpiredStatus();
                
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
                
                $machineId = $member->machine_id;
                $expiryDate = $member->expiry_date;
            }
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $member->id,
                'email' => $member->email,
                'telegram_username' => $member->telegram_username,
                'expiry_date' => $expiryDate?->toIso8601String(),
                'machine_id' => $machineId,
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at,
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

        // Enforce same device - check livekenceng subscription for backward compatibility
        $defaultApp = \App\Models\App::where('identifier', 'livekenceng')->first();
        if ($defaultApp) {
            $subscription = $member->getSubscriptionForApp($defaultApp->id);
            if ($subscription) {
                if ($subscription->machine_id && $subscription->machine_id !== $request->machine_id) {
                    return response()->json(['success' => false, 'message' => 'Machine ID mismatch'], 401);
                }
                if (!$subscription->machine_id) {
                    $subscription->machine_id = $request->machine_id;
                    $subscription->save();
                }
            }
        } else {
            // Fallback to old member table
            if ($member->machine_id && $member->machine_id !== $request->machine_id) {
                return response()->json(['success' => false, 'message' => 'Machine ID mismatch'], 401);
            }
            if (!$member->machine_id) {
                $member->machine_id = $request->machine_id;
            }
        }

        $member->password = Hash::make($request->new_password);
        $member->save();

        // Clear profile cache so new password takes effect immediately
        Cache::forget("member_profile:" . strtolower(trim($member->email)));

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }

    /**
     * Get machine ID by email
     */
    public function getMachineId(Request $request, $email)
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

        // Determine which app to use
        $appIdentifier = $request->input('app_identifier');
        
        if (!$appIdentifier) {
            // Default to legacy livekenceng app for backward compatibility
            $appIdentifier = 'livekenceng';
        }

        // Find the app by identifier
        $app = \App\Models\App::where('identifier', $appIdentifier)->first();

        if (!$app) {
            return response()->json([
                'success' => false,
                'message' => 'App not found'
            ], 404);
        }

        // Get subscription for this app
        $subscription = $member->getSubscriptionForApp($app->id);
        
        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No subscription found for this app'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'email' => $member->email,
            'machine_id' => $subscription->machine_id,
            'app_identifier' => $app->identifier
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

        // Determine which app to use
        $appIdentifier = $request->input('app_identifier');
        
        if (!$appIdentifier) {
            // Default to legacy livekenceng app for backward compatibility
            $appIdentifier = 'livekenceng';
        }

        // Find the app by identifier
        $app = \App\Models\App::where('identifier', $appIdentifier)->first();

        if (!$app) {
            return response()->json([
                'success' => false,
                'message' => 'App not found'
            ], 404);
        }

        // Get or create subscription for this app
        $subscription = $member->getSubscriptionForApp($app->id);
        
        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found for this app'
            ], 404);
        }

        // Update machine_id for this app's subscription
        $subscription->machine_id = $request->machine_id;
        $subscription->save();
        
        // Clear profile cache so updated machine_id is reflected immediately
        Cache::forget("member_profile:" . strtolower(trim($member->email)));
        
        return response()->json([
            'success' => true,
            'message' => 'Machine ID updated successfully',
            'email' => $member->email,
            'machine_id' => $subscription->machine_id,
            'app_identifier' => $app->identifier
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

        // Check if license is for a specific app (multi-app system)
        if ($licenseKey->app_id) {
            // Validate app is active
            $app = \App\Models\App::find($licenseKey->app_id);
            if (!$app || !$app->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'This license is for an inactive or invalid app'
                ], 400);
            }
            
            // NEW: Multi-app system - handle app-specific subscriptions
            if (!$member) {
                // Create new member with random password
                $generatedPassword = \Illuminate\Support\Str::random(12);
                $member = Member::create([
                    'email' => $request->email,
                    'password' => Hash::make($generatedPassword),
                ]);
                $isNewMember = true;
            }
            
            // Get or create subscription for this app
            $subscription = $member->getSubscriptionForApp($licenseKey->app_id);
            
            if (!$subscription) {
                // Create new subscription for this app
                $subscription = \App\Models\MemberSubscription::create([
                    'member_id' => $member->id,
                    'app_id' => $licenseKey->app_id,
                    'expiry_date' => now()->addDays($licenseKey->duration_days),
                ]);
            } else {
                // Calculate new expiry date for existing subscription
                $currentExpiry = $subscription->expiry_date && $subscription->expiry_date->isFuture() 
                    ? $subscription->expiry_date 
                    : now();

                $newExpiry = $currentExpiry->addDays($licenseKey->duration_days);
                
                // Update subscription
                $subscription->expiry_date = $newExpiry;
                $subscription->save();
            }
            
            $expiryDate = $subscription->expiry_date;
            
        } else {
            // BACKWARD COMPATIBILITY: Legacy licenses without app_id -> default to livekenceng app
            $defaultApp = \App\Models\App::where('identifier', 'livekenceng')->first();
            
            if ($defaultApp) {
                // Handle as livekenceng app subscription
                if (!$member) {
                    // Create new member with random password
                    $generatedPassword = \Illuminate\Support\Str::random(12);
                    $member = Member::create([
                        'email' => $request->email,
                        'password' => Hash::make($generatedPassword),
                    ]);
                    $isNewMember = true;
                }
                
                // Get or create subscription for livekenceng app
                $subscription = $member->getSubscriptionForApp($defaultApp->id);
                
                if (!$subscription) {
                    // Create new subscription for livekenceng app
                    $subscription = \App\Models\MemberSubscription::create([
                        'member_id' => $member->id,
                        'app_id' => $defaultApp->id,
                        'expiry_date' => now()->addDays($licenseKey->duration_days),
                    ]);
                } else {
                    // Calculate new expiry date for existing subscription
                    $currentExpiry = $subscription->expiry_date && $subscription->expiry_date->isFuture() 
                        ? $subscription->expiry_date 
                        : now();

                    $newExpiry = $currentExpiry->addDays($licenseKey->duration_days);
                    
                    // Update subscription
                    $subscription->expiry_date = $newExpiry;
                    $subscription->save();
                }
                
                $expiryDate = $subscription->expiry_date;
                
            } else {
                // Fallback: Use old members table if no default app exists
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
                
                $expiryDate = $member->expiry_date;
            }
        }

        // Mark license as used
        $licenseKey->is_used = true;
        $licenseKey->used_by = $member->id;
        $licenseKey->used_at = now();
        $licenseKey->save();

        // Clear profile cache so user sees updated expiry date immediately
        Cache::forget("member_profile:" . strtolower(trim($member->email)));

        $response = [
            'success' => true,
            'message' => $isNewMember 
                ? 'New account created and license activated successfully' 
                : 'License key redeemed successfully',
            'expiry_date' => $expiryDate->toIso8601String(),
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

    /**
     * Get user profile
     * OPTIMIZED: Aggressive caching to handle high-frequency polling from desktop apps
     */
    public function getProfile(Request $request)
    {
        // Validate required fields
        if (!$request->email || !$request->password) {
            return response()->json([
                'success' => false,
                'message' => 'Email and password are required'
            ], 400);
        }

        $email = strtolower(trim($request->email));
        $cacheKey = "member_profile:{$email}";
        
        // Try to get cached profile data
        $cachedData = Cache::get($cacheKey);
        
        if ($cachedData) {
            // Verify password with cached hash to ensure security
            if (!Hash::check($request->password, $cachedData['password_hash'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
            
            // Return cached profile data (without password hash)
            return response()->json([
                'success' => true,
                'data' => $cachedData['profile']
            ]);
        }

        // Cache miss - fetch from database
        $member = Member::where('email', $email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check and update expired status
        $member->checkAndUpdateExpiredStatus();

        // Get machine_id and expiry_date from livekenceng subscription for backward compatibility
        $machineId = $member->machine_id;
        $expiryDate = $member->expiry_date;
        
        $defaultApp = \App\Models\App::where('identifier', 'livekenceng')->first();
        if ($defaultApp) {
            $subscription = $member->getSubscriptionForApp($defaultApp->id);
            if ($subscription) {
                $machineId = $subscription->machine_id ?? $member->machine_id;
                $expiryDate = $subscription->expiry_date;
            }
        }

        $profileData = [
            'id' => $member->id,
            'email' => $member->email,
            'telegram_username' => $member->telegram_username,
            'expiry_date' => $expiryDate?->toIso8601String(),
            'machine_id' => $machineId,
            'created_at' => $member->created_at,
            'updated_at' => $member->updated_at,
        ];

        // Cache profile data for 1 hour (3600 seconds)
        // This dramatically reduces database load from frequent desktop app polling
        Cache::put($cacheKey, [
            'password_hash' => $member->password,
            'profile' => $profileData
        ], 3600);

        return response()->json([
            'success' => true,
            'data' => $profileData
        ]);
    }

    /**
     * Update Telegram username
     */
    public function updateTelegram(Request $request)
    {
        // Validate required fields
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

        // Check and update expired status
        $member->checkAndUpdateExpiredStatus();

        // Validate telegram username
        $request->validate([
            'telegram_username' => 'nullable|string|max:255'
        ]);

        $member->telegram_username = $request->telegram_username;
        $member->save();

        // Clear profile cache so updated telegram username is reflected immediately
        Cache::forget("member_profile:" . strtolower(trim($member->email)));

        return response()->json([
            'success' => true,
            'message' => 'Telegram username updated successfully',
            'data' => [
                'id' => $member->id,
                'email' => $member->email,
                'telegram_username' => $member->telegram_username,
                'updated_at' => $member->updated_at,
            ]
        ]);
    }

    /**
     * Get user settings (placeholder for future implementation)
     */
    public function getSettings(Request $request)
    {
        // Validate required fields
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

        // Check and update expired status
        $member->checkAndUpdateExpiredStatus();

        // Return empty settings for now (can be expanded later with a settings table)
        return response()->json([
            'success' => true,
            'data' => [
                'download_platform' => null,
                'download_duration' => null,
                'download_save_location' => null,
                'split_duration' => null,
                'part_delay_seconds' => null,
                'custom_ffmpeg_path' => null,
            ]
        ]);
    }

    /**
     * Update user settings (placeholder for future implementation)
     */
    public function updateSettings(Request $request)
    {
        // Validate required fields
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

        // Check and update expired status
        $member->checkAndUpdateExpiredStatus();

        // Validate settings
        $request->validate([
            'settings' => 'nullable|array',
            'settings.download_platform' => 'nullable|string',
            'settings.download_duration' => 'nullable|integer',
            'settings.download_save_location' => 'nullable|string',
            'settings.split_duration' => 'nullable|integer',
            'settings.part_delay_seconds' => 'nullable|integer',
            'settings.custom_ffmpeg_path' => 'nullable|string',
        ]);

        // For now, just return success (can be expanded later with a settings table)
        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $request->settings ?? []
        ]);
    }
}
