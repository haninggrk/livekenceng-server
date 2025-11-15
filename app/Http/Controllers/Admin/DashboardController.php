<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\LicenseKey;
use App\Models\Reseller;
use App\Models\LicensePlan;
use App\Models\Niche;
use App\Models\ProductSet;
use App\Models\ProductSetItem;
use App\Models\DeviceMetadata;
use App\Models\MemberSubscription;
use App\Services\ShopeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        $members = Member::orderBy('created_at', 'desc')->get();
        
        $licenseKeys = LicenseKey::with(['member', 'creator', 'reseller', 'app'])
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
        $query = Member::with(['licenseKeys', 'subscriptions.app']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('telegram_username', 'like', "%{$search}%");
            });
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
        $member->load(['subscriptions.app', 'shopeeAccounts', 'deviceMetadata']);
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
            'telegram_username' => 'nullable|string|max:255',
        ]);

        $member = Member::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'telegram_username' => $validated['telegram_username'] ?? null,
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
            'telegram_username' => 'nullable|string|max:255',
        ]);

        $member->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $member->password = Hash::make($validated['password']);
        }

        if (isset($validated['telegram_username'])) {
            $member->telegram_username = $validated['telegram_username'];
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
            'app_id' => 'nullable|integer|exists:apps,id',
        ]);

        $plan = LicensePlan::findOrFail($validated['plan_id']);

        $licenses = [];
        for ($i = 0; $i < $validated['quantity']; $i++) {
            $license = LicenseKey::create([
                'code' => LicenseKey::generateCode(),
                'duration_days' => $plan->duration_days,
                'plan_id' => $plan->id,
                'app_id' => $validated['app_id'] ?? null,
                'price' => (float)$plan->price,
                'created_by' => Auth::user()->id,
                'reseller_id' => $validated['reseller_id'] ?? null,
            ]);
            $licenses[] = $license;

            // If generated by reseller, track the purchase
            if (isset($validated['reseller_id']) && $validated['reseller_id']) {
                $reseller = Reseller::find($validated['reseller_id']);
                $discountedPrice = $reseller->calculatePrice((float)$plan->price);
                $reseller->trackPurchase($discountedPrice, (float)$plan->price);
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
        $licenses = LicenseKey::with(['member', 'creator', 'reseller', 'app'])
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
    public function getPlans(Request $request)
    {
        $query = LicensePlan::query();
        
        // Filter by app_id if provided
        if ($request->has('app_id')) {
            if ($request->app_id === null || $request->app_id === '') {
                // Null app_id means livekenceng (legacy)
                $livekencengApp = \App\Models\App::where('identifier', 'livekenceng')->first();
                if ($livekencengApp) {
                    $query->where('app_id', $livekencengApp->id);
                }
            } else {
                $query->where('app_id', $request->app_id);
            }
        }
        
        $plans = $query->with('app')->orderBy('duration_days')->get();
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
            'app_id' => 'required|integer|exists:apps,id',
            'name' => 'nullable|string|max:100',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        // Check for duplicate duration_days within same app
        $existingPlan = LicensePlan::where('app_id', $validated['app_id'])
            ->where('duration_days', $validated['duration_days'])
            ->first();
        
        if ($existingPlan) {
            return response()->json([
                'success' => false,
                'message' => 'A plan with this duration already exists for this app',
            ], 400);
        }

        $plan = LicensePlan::create([
            'app_id' => $validated['app_id'],
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
            'app_id' => 'required|integer|exists:apps,id',
            'name' => 'nullable|string|max:100',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        // Check for duplicate duration_days within same app (excluding current plan)
        $existingPlan = LicensePlan::where('app_id', $validated['app_id'])
            ->where('duration_days', $validated['duration_days'])
            ->where('id', '!=', $plan->id)
            ->first();
        
        if ($existingPlan) {
            return response()->json([
                'success' => false,
                'message' => 'A plan with this duration already exists for this app',
            ], 400);
        }

        $plan->app_id = $validated['app_id'];
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

    /**
     * Get all niches for a member
     */
    public function getMemberNiches(Member $member)
    {
        $niches = $member->niches()->with(['productSets.items'])->orderBy('created_at', 'desc')->get();
        
        // Also get product sets without niche
        $productSetsWithoutNiche = $member->productSets()
            ->whereNull('niche_id')
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'niches' => $niches,
            'product_sets_without_niche' => $productSetsWithoutNiche
        ]);
    }

    /**
     * Create a new niche
     */
    public function createNiche(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $niche = Niche::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Niche created successfully',
            'niche' => $niche->load('productSets')
        ]);
    }

    /**
     * Update a niche
     */
    public function updateNiche(Request $request, Niche $niche)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $niche->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Niche updated successfully',
            'niche' => $niche->fresh(['productSets.items'])
        ]);
    }

    /**
     * Delete a niche
     */
    public function deleteNiche(Niche $niche)
    {
        $niche->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Niche deleted successfully'
        ]);
    }

    /**
     * Export niche to CSV
     */
    public function exportNicheToCSV(Niche $niche)
    {
        $niche->load(['productSets.items', 'member']);
        
        $filename = 'niche_' . $niche->id . '_' . Str::slug($niche->name) . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($niche) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Niche: ' . $niche->name]);
            fputcsv($file, ['Description: ' . ($niche->description ?? 'N/A')]);
            fputcsv($file, ['Member: ' . $niche->member->email]);
            fputcsv($file, ['Created: ' . $niche->created_at->format('Y-m-d H:i:s')]);
            fputcsv($file, []); // Empty row
            
            foreach ($niche->productSets as $productSet) {
                fputcsv($file, ['Product Set: ' . $productSet->name]);
                fputcsv($file, ['Product Set Description: ' . ($productSet->description ?? 'N/A')]);
                fputcsv($file, ['Items Count: ' . $productSet->items->count()]);
                fputcsv($file, []); // Empty row
                
                // Items header
                fputcsv($file, ['Item URL', 'Shop ID', 'Item ID']);
                
                foreach ($productSet->items as $item) {
                    fputcsv($file, [
                        $item->url,
                        $item->shop_id,
                        $item->item_id
                    ]);
                }
                
                fputcsv($file, []); // Empty row between product sets
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Get all product sets for a member
     */
    public function getMemberProductSets(Member $member)
    {
        $productSets = $member->productSets()->with(['niche', 'items'])->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'product_sets' => $productSets
        ]);
    }

    /**
     * Create a new product set
     */
    public function createProductSet(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'niche_id' => 'nullable|exists:niches,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Verify niche belongs to member if provided
        if ($validated['niche_id']) {
            $niche = Niche::find($validated['niche_id']);
            if (!$niche || $niche->member_id != $validated['member_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Niche does not belong to this member'
                ], 400);
            }
        }

        $productSet = ProductSet::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product set created successfully',
            'product_set' => $productSet->load(['niche', 'items'])
        ]);
    }

    /**
     * Update a product set
     */
    public function updateProductSet(Request $request, ProductSet $productSet)
    {
        $validated = $request->validate([
            'niche_id' => 'nullable|exists:niches,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Verify niche belongs to member if provided
        if ($validated['niche_id']) {
            $niche = Niche::find($validated['niche_id']);
            if (!$niche || $niche->member_id != $productSet->member_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Niche does not belong to this member'
                ], 400);
            }
        }

        $productSet->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product set updated successfully',
            'product_set' => $productSet->fresh(['niche', 'items'])
        ]);
    }

    /**
     * Delete a product set
     */
    public function deleteProductSet(ProductSet $productSet)
    {
        $productSet->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Product set deleted successfully'
        ]);
    }

    /**
     * Add items to a product set
     */
    public function addProductSetItems(Request $request, ProductSet $productSet)
    {
        $validated = $request->validate([
            'items' => 'required|array|max:100',
            'items.*' => 'required|array',
            'items.*.url' => 'required|string',
            'items.*.shop_id' => 'sometimes|integer',
            'items.*.item_id' => 'sometimes|integer',
        ]);

        $currentItemCount = $productSet->items()->count();
        $newItemsCount = count($validated['items']);

        if ($currentItemCount + $newItemsCount > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Product set cannot have more than 100 items. Current: ' . $currentItemCount . ', Trying to add: ' . $newItemsCount
            ], 400);
        }

        $addedItems = [];
        $skippedItems = [];

        foreach ($validated['items'] as $itemData) {
            $url = $itemData['url'];
            
            // Check for duplicate URL
            $existingItem = $productSet->items()->where('url', $url)->first();
            
            if ($existingItem) {
                $skippedItems[] = [
                    'url' => $url,
                    'reason' => 'Duplicate URL'
                ];
                continue;
            }

            // Parse URL if shop_id/item_id not provided
            $shopId = $itemData['shop_id'] ?? null;
            $itemId = $itemData['item_id'] ?? null;
            
            if (!$shopId || !$itemId) {
                $parsed = $this->parseProductUrl($url);
                if (!$parsed) {
                    $skippedItems[] = [
                        'url' => $url,
                        'reason' => 'Invalid URL format'
                    ];
                    continue;
                }
                $shopId = $parsed['shop_id'];
                $itemId = $parsed['item_id'];
            }

            try {
                $item = ProductSetItem::create([
                    'product_set_id' => $productSet->id,
                    'url' => $url,
                    'shop_id' => $shopId,
                    'item_id' => $itemId,
                ]);
                $addedItems[] = $item;
            } catch (\Exception $e) {
                $skippedItems[] = [
                    'url' => $url,
                    'reason' => 'Error: ' . $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Items processed',
            'added' => count($addedItems),
            'skipped' => count($skippedItems),
            'added_items' => $addedItems,
            'skipped_items' => $skippedItems,
        ]);
    }

    /**
     * Parse product URL to extract shop_id and item_id
     */
    private function parseProductUrl(string $url): ?array
    {
        if (preg_match('/\/product\/(\d+)\/(\d+)/', $url, $matches)) {
            return [
                'shop_id' => (int)$matches[1],
                'item_id' => (int)$matches[2],
            ];
        }
        return null;
    }

    /**
     * Delete a product set item
     */
    public function deleteProductSetItem(ProductSet $productSet, ProductSetItem $item)
    {
        if ($item->product_set_id !== $productSet->id) {
            return response()->json([
                'success' => false,
                'message' => 'Item does not belong to this product set'
            ], 400);
        }

        $item->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully'
        ]);
    }

    /**
     * Clear all items from a product set
     */
    public function clearProductSetItems(ProductSet $productSet)
    {
        $productSet->items()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'All items cleared successfully'
        ]);
    }

    /**
     * Export product set to CSV
     */
    public function exportProductSetToCSV(ProductSet $productSet)
    {
        $productSet->load(['items', 'niche', 'member']);
        
        $filename = 'product_set_' . $productSet->id . '_' . Str::slug($productSet->name) . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($productSet) {
            $file = fopen('php://output', 'w');
            
            // Header rows
            fputcsv($file, ['Product Set: ' . $productSet->name]);
            fputcsv($file, ['Description: ' . ($productSet->description ?? 'N/A')]);
            fputcsv($file, ['Niche: ' . ($productSet->niche->name ?? 'No Niche')]);
            fputcsv($file, ['Member: ' . $productSet->member->email]);
            fputcsv($file, ['Items Count: ' . $productSet->items->count()]);
            fputcsv($file, ['Created: ' . $productSet->created_at->format('Y-m-d H:i:s')]);
            fputcsv($file, []); // Empty row
            
            // Items header
            fputcsv($file, ['URL', 'Shop ID', 'Item ID']);
            
            foreach ($productSet->items as $item) {
                fputcsv($file, [
                    $item->url,
                    $item->shop_id,
                    $item->item_id
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Get all active livestreams from all members
     */
    public function getActiveLivestreams(Request $request, ShopeeService $shopeeService)
    {
        // Get all active members with active Shopee accounts
        $members = Member::with(['shopeeAccounts' => function($query) {
            $query->where('is_active', true);
        }])->get();

        $activeLivestreams = [];

        foreach ($members as $member) {
            foreach ($member->shopeeAccounts as $account) {
                $sessionData = $shopeeService->getActiveSessionData($account->cookie);
                
                if ($sessionData && $sessionData['session_id']) {
                    $activeLivestreams[] = [
                        'member_email' => $member->email,
                        'account_name' => $account->name,
                        'account_id' => $account->id,
                        'session_id' => $sessionData['session_id'],
                        'gmv' => $sessionData['gmv'],
                        'views' => $sessionData['views'],
                        'likes' => $sessionData['likes'],
                        'comments' => $sessionData['comments'],
                        'atc' => $sessionData['atc'],
                        'placed_orders' => $sessionData['placed_orders'],
                        'confirmed_orders' => $sessionData['confirmed_orders']
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'livestreams' => $activeLivestreams,
            'total' => count($activeLivestreams)
        ]);
    }

    /**
     * Update subscription expiry date
     */
    public function updateSubscriptionExpiry(Request $request, MemberSubscription $subscription)
    {
        $validated = $request->validate([
            'expiry_date' => 'nullable|date',
        ]);

        $expiryDate = $validated['expiry_date'] ?? null;

        if ($expiryDate) {
            $subscription->expiry_date = Carbon::parse($expiryDate);
        } else {
            $subscription->expiry_date = null;
        }

        $subscription->save();

        return response()->json([
            'success' => true,
            'message' => 'Subscription expiry updated successfully',
            'expiry_date' => $subscription->expiry_date ? $subscription->expiry_date->toIso8601String() : null,
            'expiry_display' => $subscription->expiry_date ? $subscription->expiry_date->timezone(config('app.timezone'))->format('Y-m-d H:i') : null,
            'is_active' => $subscription->isActive(),
        ]);
    }

    /**
     * Update subscription machine ID
     */
    public function updateSubscriptionMachineId(Request $request, MemberSubscription $subscription)
    {
        $validated = $request->validate([
            'machine_id' => 'nullable|string|max:255',
        ]);

        $subscription->machine_id = $validated['machine_id'] ?: null;
        $subscription->save();

        return response()->json([
            'success' => true,
            'message' => 'Subscription machine ID updated successfully',
            'machine_id' => $subscription->machine_id,
        ]);
    }

    /**
     * List expired subscriptions for follow-up
     */
    public function getExpiredSubscriptions(Request $request)
    {
        $query = MemberSubscription::with([
            'member:id,email,telegram_username',
            'app:id,display_name,identifier',
        ])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', Carbon::now());

        if ($search = trim((string) $request->get('search'))) {
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('telegram_username', 'like', "%{$search}%");
            });
        }

        if ($appIdentifier = $request->get('app_identifier')) {
            $query->whereHas('app', function ($q) use ($appIdentifier) {
                $q->where('identifier', $appIdentifier);
            });
        }

        $subscriptions = $query
            ->orderBy('expiry_date', 'asc')
            ->get()
            ->map(function (MemberSubscription $subscription) {
                return [
                    'id' => $subscription->id,
                    'member' => $subscription->member ? [
                        'id' => $subscription->member->id,
                        'email' => $subscription->member->email,
                        'telegram_username' => $subscription->member->telegram_username,
                    ] : null,
                    'app' => $subscription->app ? [
                        'id' => $subscription->app->id,
                        'identifier' => $subscription->app->identifier,
                        'display_name' => $subscription->app->display_name,
                    ] : null,
                    'machine_id' => $subscription->machine_id,
                    'expiry_date' => $subscription->expiry_date?->toIso8601String(),
                    'expired_days' => $subscription->expiry_date ? $subscription->expiry_date->diffInDays(Carbon::now()) : null,
                ];
            });

        return response()->json([
            'success' => true,
            'subscriptions' => $subscriptions,
            'total' => $subscriptions->count(),
        ]);
    }

    /**
     * Get all device metadata for a member
     */
    public function getMemberDeviceMetadata(Member $member)
    {
        $devices = $member->deviceMetadata()->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'devices' => $devices
        ]);
    }

    /**
     * Create a new device metadata
     */
    public function createDeviceMetadata(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'manufacturer' => 'nullable|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'device_model' => 'nullable|string|max:255',
            'rom' => 'nullable|string|max:255',
        ]);

        $device = DeviceMetadata::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Device metadata created successfully',
            'device' => $device
        ]);
    }

    /**
     * Update a device metadata
     */
    public function updateDeviceMetadata(Request $request, DeviceMetadata $deviceMetadata)
    {
        $validated = $request->validate([
            'manufacturer' => 'nullable|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'device_model' => 'nullable|string|max:255',
            'rom' => 'nullable|string|max:255',
        ]);

        $deviceMetadata->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Device metadata updated successfully',
            'device' => $deviceMetadata->fresh()
        ]);
    }

    /**
     * Delete a device metadata
     */
    public function deleteDeviceMetadata(DeviceMetadata $deviceMetadata)
    {
        $deviceMetadata->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Device metadata deleted successfully'
        ]);
    }
}
