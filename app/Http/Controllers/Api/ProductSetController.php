<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\ProductSet;
use App\Models\ProductSetItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProductSetController extends Controller
{
    /**
     * Get all product sets for authenticated member
     */
    public function index(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $productSets = $member->productSets()->with(['niche', 'items'])->get();

        return response()->json([
            'success' => true,
            'product_sets' => $productSets
        ]);
    }

    /**
     * Get a single product set
     */
    public function show(Request $request, ProductSet $productSet)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($productSet->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product set not found'
            ], 404);
        }

        $productSet->load(['niche', 'items']);

        return response()->json([
            'success' => true,
            'product_set' => $productSet
        ]);
    }

    /**
     * Create a new product set
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'niche_id' => 'nullable|exists:niches,id',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Verify niche belongs to member if provided
        if ($request->niche_id) {
            $niche = $member->niches()->find($request->niche_id);
            if (!$niche) {
                return response()->json([
                    'success' => false,
                    'message' => 'Niche not found or does not belong to you'
                ], 404);
            }
        }

        $productSet = ProductSet::create([
            'member_id' => $member->id,
            'niche_id' => $request->niche_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product set created successfully',
            'product_set' => $productSet
        ], 201);
    }

    /**
     * Update a product set
     */
    public function update(Request $request, ProductSet $productSet)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'niche_id' => 'nullable|exists:niches,id',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($productSet->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product set not found'
            ], 404);
        }

        // Verify niche belongs to member if provided
        if ($request->has('niche_id') && $request->niche_id) {
            $niche = $member->niches()->find($request->niche_id);
            if (!$niche) {
                return response()->json([
                    'success' => false,
                    'message' => 'Niche not found or does not belong to you'
                ], 404);
            }
        }

        $productSet->update([
            'name' => $request->input('name', $productSet->name),
            'description' => $request->input('description', $productSet->description),
            'niche_id' => $request->has('niche_id') ? $request->niche_id : $productSet->niche_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product set updated successfully',
            'product_set' => $productSet->fresh(['niche', 'items'])
        ]);
    }

    /**
     * Delete a product set
     */
    public function destroy(Request $request, ProductSet $productSet)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($productSet->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product set not found'
            ], 404);
        }

        $productSet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product set deleted successfully'
        ]);
    }

    /**
     * Parse URL to extract shop_id and item_id
     */
    private function parseProductUrl(string $url): ?array
    {
        // Match pattern: /product/{shop_id}/{item_id}
        if (preg_match('/\/product\/(\d+)\/(\d+)/', $url, $matches)) {
            return [
                'shop_id' => (int)$matches[1],
                'item_id' => (int)$matches[2],
            ];
        }
        return null;
    }

    /**
     * Add items to a product set
     * Items can be sent with either:
     * 1. url, shop_id, item_id (already parsed)
     * 2. url only (will be parsed automatically)
     */
    public function addItems(Request $request, ProductSet $productSet)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'items' => 'required|array|max:100',
            'items.*' => 'required|array',
            'items.*.url' => 'required|string',
            'items.*.shop_id' => 'sometimes|integer',
            'items.*.item_id' => 'sometimes|integer',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($productSet->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product set not found'
            ], 404);
        }

        $currentItemCount = $productSet->items()->count();
        $newItemsCount = count($request->items);

        if ($currentItemCount + $newItemsCount > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Product set cannot have more than 100 items. Current: ' . $currentItemCount . ', Trying to add: ' . $newItemsCount
            ], 400);
        }

        $addedItems = [];
        $skippedItems = [];

        foreach ($request->items as $itemData) {
            $url = $itemData['url'];
            
            // Check for duplicate URL in this product set
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
                        'reason' => 'Invalid URL format. Expected: shopee.co.id/product/{shop_id}/{item_id}'
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
     * Remove an item from a product set
     */
    public function removeItem(Request $request, ProductSet $productSet, ProductSetItem $item)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($productSet->member_id !== $member->id || $item->product_set_id !== $productSet->id) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed successfully'
        ]);
    }

    /**
     * Clear all items from a product set
     */
    public function clearItems(Request $request, ProductSet $productSet)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($productSet->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product set not found'
            ], 404);
        }

        $productSet->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'All items cleared successfully'
        ]);
    }
}
