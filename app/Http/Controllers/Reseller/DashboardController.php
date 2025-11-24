<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\LicenseKey;
use App\Models\LicensePlan;
use App\Models\Reseller;
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
            ->with(['member', 'app'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get only apps that this reseller can purchase licenses for
        $apps = $reseller->allowedApps()
            ->withCount(['licensePlans' => function ($query) {
                $query->where('is_active', true);
            }])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reseller.dashboard', compact('reseller', 'licenses', 'apps'));
    }

    /**
     * Get license pricing
     */
    public function getPricing(Request $request)
    {
        $reseller = Auth::guard('reseller')->user();

        $query = LicensePlan::where('is_active', true);

        // Filter by app_id if provided
        if ($request->has('app_id')) {
            if ($request->app_id === null || $request->app_id === '') {
                // Null app_id means livekenceng (legacy)
                $livekencengApp = App::where('identifier', 'livekenceng')->first();
                if ($livekencengApp) {
                    $query->where('app_id', $livekencengApp->id);
                }
            } else {
                $appId = $request->app_id;
                // Verify reseller can purchase this app
                if (! $reseller->canPurchaseApp($appId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not authorized to purchase licenses for this app.',
                    ], 403);
                }
                $query->where('app_id', $appId);
            }
        } else {
            // If no app_id specified, only show plans for apps the reseller can purchase
            $allowedAppIds = $reseller->allowedApps()->pluck('apps.id')->toArray();
            if (! empty($allowedAppIds)) {
                $query->whereIn('app_id', $allowedAppIds);
            } else {
                // If reseller has no allowed apps, return empty result
                $query->whereRaw('1 = 0');
            }
        }

        $plans = $query->orderBy('duration_days')->get();
        $pricing = $plans->map(function ($plan) use ($reseller) {
            $basePrice = (float) $plan->price;

            return [
                'plan_id' => $plan->id,
                'name' => $plan->name,
                'duration_days' => $plan->duration_days,
                'base_price' => $basePrice,
                'discount' => (float) $reseller->discount_percentage,
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
            'plan_id' => 'required|integer|exists:license_plans,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $reseller = Auth::guard('reseller')->user();

        $plan = LicensePlan::where('is_active', true)->findOrFail($validated['plan_id']);
        $basePrice = (float) $plan->price;
        $durationDays = (int) $plan->duration_days;

        // Check if reseller can purchase licenses for this app
        if ($plan->app_id && ! $reseller->canPurchaseApp($plan->app_id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to purchase licenses for this app.',
            ], 403);
        }

        $finalPrice = $reseller->calculatePrice($basePrice);
        $totalCost = $finalPrice * $validated['quantity'];

        // Check if reseller can afford
        if (! $reseller->canAfford($totalCost)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance. Required: Rp '.number_format($totalCost, 0, ',', '.'),
            ], 400);
        }

        $licenses = [];
        for ($i = 0; $i < $validated['quantity']; $i++) {
            $license = LicenseKey::create([
                'code' => LicenseKey::generateCode(),
                'duration_days' => $durationDays,
                'plan_id' => $plan->id,
                'app_id' => $plan->app_id, // Include app_id from plan
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
            ->with(['member', 'app'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'licenses' => $licenses,
        ]);
    }
}
