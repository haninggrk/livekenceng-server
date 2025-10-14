<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\LicenseKey;
use App\Models\LicensePlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show reseller dashboard
     */
    public function index()
    {
        $reseller = Auth::guard('reseller')->user();
        $licenses = LicenseKey::where('reseller_id', $reseller->id)
            ->with(['member'])
            ->orderBy('created_at', 'desc')
            ->get();
        $plans = LicensePlan::where('is_active', true)->orderBy('duration_days')->get();
        
        return view('reseller.dashboard', compact('reseller', 'licenses', 'plans'));
    }

    /**
     * Get license pricing
     */
    public function getPricing()
    {
        $reseller = Auth::guard('reseller')->user();
        $plans = LicensePlan::where('is_active', true)->orderBy('duration_days')->get();
        $pricing = $plans->map(function ($plan) use ($reseller) {
            $basePrice = (float)$plan->price;
            return [
                'plan_id' => $plan->id,
                'name' => $plan->name,
                'duration_days' => $plan->duration_days,
                'base_price' => $basePrice,
                'discount' => (float)$reseller->discount_percentage,
                'final_price' => $reseller->calculatePrice($basePrice),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'pricing' => $pricing,
            'balance' => $reseller->balance,
        ]);
    }

    /**
     * Generate license key
     */
    public function generateLicense(Request $request)
    {
        $validated = $request->validate([
            'duration_days' => 'required_without:plan_id|in:1,3,7,14,30',
            'plan_id' => 'required_without:duration_days|integer|exists:license_plans,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $reseller = Auth::guard('reseller')->user();

        // Prefer plan_id if provided; fallback to duration_days (legacy)
        $plan = null;
        if ($request->filled('plan_id')) {
            $plan = LicensePlan::where('is_active', true)->findOrFail($request->input('plan_id'));
            $basePrice = (float)$plan->price;
            $durationDays = (int)$plan->duration_days;
        } else {
            // Legacy fallback
            $durationDays = (int)$validated['duration_days'];
            $plan = LicensePlan::where('is_active', true)->where('duration_days', $durationDays)->first();
            $basePrice = $plan ? (float)$plan->price : match($durationDays) {
                1 => 10000,
                3 => 25000,
                7 => 40000,
                14 => 70000,
                30 => 139000,
                default => 0,
            };
        }

        $finalPrice = $reseller->calculatePrice($basePrice);
        $totalCost = $finalPrice * $validated['quantity'];

        // Check if reseller can afford
        if (!$reseller->canAfford($totalCost)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance. Required: Rp ' . number_format($totalCost, 0, ',', '.'),
            ], 400);
        }

        $licenses = [];
        for ($i = 0; $i < $validated['quantity']; $i++) {
            $license = LicenseKey::create([
                'code' => LicenseKey::generateCode(),
                'duration_days' => $durationDays,
                'plan_id' => $plan?->id,
                'price' => $finalPrice,
                'reseller_id' => $reseller->id,
                // created_by is for admin; set to null; track reseller creator explicitly
                'created_by' => null,
                'created_by_reseller_id' => $reseller->id,
            ]);
            $licenses[] = $license;
        }

        // Deduct balance
        $reseller->deductBalance($totalCost);

        return response()->json([
            'success' => true,
            'message' => 'License keys generated successfully',
            'licenses' => $licenses,
            'total_cost' => $totalCost,
            'remaining_balance' => $reseller->balance,
        ]);
    }

    /**
     * Get reseller licenses (AJAX)
     */
    public function getLicenses()
    {
        $reseller = Auth::guard('reseller')->user();
        $licenses = LicenseKey::where('reseller_id', $reseller->id)
            ->with(['member'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'licenses' => $licenses
        ]);
    }
}
