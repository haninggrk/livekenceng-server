<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\LicenseKey;
use App\Models\Reseller;
use App\Models\LicensePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        $members = Member::orderBy('created_at', 'desc')->get();
        $licenseKeys = LicenseKey::with(['member', 'creator', 'reseller'])
            ->orderBy('created_at', 'desc')
            ->get();
        $resellers = Reseller::orderBy('created_at', 'desc')->get();
        $plans = LicensePlan::orderBy('duration_days')->get();
        
        return view('admin.dashboard', compact('members', 'licenseKeys', 'resellers', 'plans'));
    }

    /**
     * Get all members (AJAX)
     */
    public function getMembers(Request $request)
    {
        $query = Member::with(['licenseKeys']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('telegram_username', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'active') {
                $query->where(function($q) {
                    $q->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>', now());
                });
            } elseif ($request->status === 'expired') {
                $query->where('expiry_date', '<=', now());
            }
        }

        // Sorting
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $sortBy = $request->sort_by;
            switch ($sortBy) {
                case 'created_at_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'created_at_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'expiry_date_asc':
                    $query->orderBy('expiry_date', 'asc');
                    break;
                case 'expiry_date_desc':
                    $query->orderBy('expiry_date', 'desc');
                    break;
                case 'email_asc':
                    $query->orderBy('email', 'asc');
                    break;
                case 'email_desc':
                    $query->orderBy('email', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $members = $query->get();
        
        return response()->json([
            'success' => true,
            'members' => $members
        ]);
    }

    /**
     * Show member edit page
     */
    public function editMember(Member $member)
    {
        $member->load(['shopeeAccounts']);
        return view('admin.members.edit', compact('member'));
    }

    /**
     * Create new member
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:members,email',
            'password' => 'required|min:6',
            'machine_id' => 'nullable|string',
            'telegram_username' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
        ]);

        $member = Member::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'machine_id' => $validated['machine_id'] ?? null,
            'telegram_username' => $validated['telegram_username'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Member created successfully',
            'member' => $member
        ]);
    }

    /**
     * Update member
     */
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:members,email,' . $member->id,
            'password' => 'nullable|min:6',
            'machine_id' => 'nullable|string',
            'telegram_username' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
        ]);

        $member->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $member->password = Hash::make($validated['password']);
        }

        if (isset($validated['machine_id'])) {
            $member->machine_id = $validated['machine_id'];
        }

        if (isset($validated['telegram_username'])) {
            $member->telegram_username = $validated['telegram_username'];
        }

        if (isset($validated['expiry_date'])) {
            $member->expiry_date = $validated['expiry_date'];
        }

        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Member updated successfully',
            'member' => $member->fresh()
        ]);
    }

    /**
     * Delete member
     */
    public function destroy(Member $member)
    {
        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member deleted successfully'
        ]);
    }

    /**
     * Reset member password
     */
    public function resetPassword(Member $member)
    {
        $newPassword = Str::random(10);
        $member->password = Hash::make($newPassword);
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully',
            'new_password' => $newPassword,
            'email' => $member->email
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
            'reseller_id' => 'nullable|integer|exists:resellers,id',
        ]);

        $plan = LicensePlan::findOrFail($validated['plan_id']);

        $licenses = [];
        for ($i = 0; $i < $validated['quantity']; $i++) {
            $license = LicenseKey::create([
                'code' => LicenseKey::generateCode(),
                'duration_days' => $plan->duration_days,
                'plan_id' => $plan->id,
                'price' => (float)$plan->price,
                'created_by' => auth()->user()->id,
                'reseller_id' => $validated['reseller_id'] ?? null,
            ]);
            $licenses[] = $license;

            // If generated by reseller, track the purchase
            if ($validated['reseller_id']) {
                $reseller = Reseller::find($validated['reseller_id']);
                $discountedPrice = $reseller->calculatePrice($plan->price);
                $reseller->trackPurchase($discountedPrice, $plan->price);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'License keys generated successfully',
            'licenses' => $licenses
        ]);
    }

    /**
     * Get all license keys (AJAX)
     */
    public function getLicenses()
    {
        $licenses = LicenseKey::with(['member', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'licenses' => $licenses
        ]);
    }

    /**
     * Delete license key
     */
    public function deleteLicense(LicenseKey $license)
    {
        $license->delete();

        return response()->json([
            'success' => true,
            'message' => 'License key deleted successfully'
        ]);
    }

    /**
     * Get all resellers (AJAX)
     */
    public function getResellers()
    {
        $resellers = Reseller::withCount('licenseKeys')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'resellers' => $resellers
        ]);
    }

    /**
     * Create new reseller
     */
    public function createReseller(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:resellers,email',
            'password' => 'required|min:6',
            'balance' => 'required|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $reseller = Reseller::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'balance' => $validated['balance'],
            'discount_percentage' => $validated['discount_percentage'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reseller created successfully',
            'reseller' => $reseller
        ]);
    }

    /**
     * Update reseller
     */
    public function updateReseller(Request $request, Reseller $reseller)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:resellers,email,' . $reseller->id,
            'password' => 'nullable|min:6',
            'balance' => 'required|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $reseller->name = $validated['name'];
        $reseller->email = $validated['email'];
        $reseller->balance = $validated['balance'];
        $reseller->discount_percentage = $validated['discount_percentage'];

        if (!empty($validated['password'])) {
            $reseller->password = Hash::make($validated['password']);
        }

        $reseller->save();

        return response()->json([
            'success' => true,
            'message' => 'Reseller updated successfully',
            'reseller' => $reseller->fresh()
        ]);
    }

    /**
     * Delete reseller
     */
    public function deleteReseller(Reseller $reseller)
    {
        $reseller->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reseller deleted successfully'
        ]);
    }

    /**
     * Add balance to reseller
     */
    public function addBalance(Request $request, Reseller $reseller)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $reseller->addBalance($validated['amount']);

        return response()->json([
            'success' => true,
            'message' => 'Balance added successfully',
            'new_balance' => $reseller->balance
        ]);
    }

    /**
     * Update license key price
     */
    public function updateLicensePrice(Request $request)
    {
        $validated = $request->validate([
            'duration_days' => 'required|in:1,3,7,14,30',
            'price' => 'required|numeric|min:0',
        ]);

        // Store price override in cache (persists across requests)
        cache()->forever("license_price_{$validated['duration_days']}", $validated['price']);

        return response()->json([
            'success' => true,
            'message' => 'License price updated successfully',
            'price' => $validated['price']
        ]);
    }

    /**
     * Get current license prices
     */
    public function getLicensePrices()
    {
        $prices = [];
        foreach ([1, 3, 7, 14, 30] as $duration) {
            $prices[$duration] = cache("license_price_{$duration}", config("licenses.prices.{$duration}"));
        }

        return response()->json([
            'success' => true,
            'prices' => $prices
        ]);
    }

    /**
     * List all license plans (AJAX)
     */
    public function getPlans()
    {
        $plans = LicensePlan::orderBy('duration_days')->get();
        return response()->json([
            'success' => true,
            'plans' => $plans,
        ]);
    }

    /**
     * Create a new license plan
     */
    public function createPlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
            'duration_days' => 'required|integer|min:1|unique:license_plans,duration_days',
            'price' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $plan = LicensePlan::create([
            'name' => $validated['name'] ?? null,
            'duration_days' => $validated['duration_days'],
            'price' => $validated['price'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully',
            'plan' => $plan,
        ]);
    }

    /**
     * Update an existing license plan
     */
    public function updatePlan(Request $request, LicensePlan $plan)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
            'duration_days' => 'required|integer|min:1|unique:license_plans,duration_days,' . $plan->id,
            'price' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $plan->name = $validated['name'] ?? null;
        $plan->duration_days = $validated['duration_days'];
        $plan->price = $validated['price'];
        if (array_key_exists('is_active', $validated)) {
            $plan->is_active = (bool)$validated['is_active'];
        }
        $plan->save();

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully',
            'plan' => $plan->fresh(),
        ]);
    }

    /**
     * Delete a license plan
     */
    public function deletePlan(LicensePlan $plan)
    {
        $plan->delete();
        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully',
        ]);
    }
}
