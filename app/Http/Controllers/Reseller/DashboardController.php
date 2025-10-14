<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\LicenseKey;
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
        
        return view('reseller.dashboard', compact('reseller', 'licenses'));
    }

    /**
     * Get license pricing
     */
    public function getPricing()
    {
        // Define base pricing for each duration
        $basePricing = [
            1 => 10000,   // 1 day
            3 => 25000,   // 3 days
            7 => 40000,   // 7 days
            14 => 70000,  // 14 days
            30 => 139000, // 30 days
        ];

        $reseller = Auth::guard('reseller')->user();
        $pricing = [];

        foreach ($basePricing as $days => $basePrice) {
            $pricing[$days] = [
                'base_price' => $basePrice,
                'discount' => $reseller->discount_percentage,
                'final_price' => $reseller->calculatePrice($basePrice),
            ];
        }

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
            'duration_days' => 'required|in:1,3,7,14,30',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $reseller = Auth::guard('reseller')->user();

        // Define base pricing
        $basePricing = [
            1 => 10000,
            3 => 25000,
            7 => 40000,
            14 => 70000,
            30 => 139000,
        ];

        $basePrice = $basePricing[$validated['duration_days']];
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
                'duration_days' => $validated['duration_days'],
                'price' => $finalPrice,
                'reseller_id' => $reseller->id,
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
