<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoftwareUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UpdateController extends Controller
{
    /**
     * Show updates management page
     */
    public function index()
    {
        $updates = SoftwareUpdate::orderBy('created_at', 'desc')->get();
        return view('admin.updates.index', compact('updates'));
    }

    /**
     * Show create update form
     */
    public function create()
    {
        return view('admin.updates.create');
    }

    /**
     * Store new update
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'target' => 'required|string|max:255',
            'version' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'pub_date' => 'required|date',
            'platforms' => 'required|array',
            'platforms.*.name' => 'required|string',
            'platforms.*.signature' => 'nullable|string',
            'platforms.*.file' => 'required|file|mimes:msi,exe,deb,rpm,dmg,pkg|max:102400', // 100MB max
            'is_latest' => 'boolean',
            'download_url' => 'nullable|string|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['target', 'version', 'notes', 'pub_date', 'is_latest', 'download_url']);
        $data['pub_date'] = \Carbon\Carbon::parse($request->pub_date);
        
        // Handle file uploads and create platforms array
        $platforms = [];
        foreach ($request->platforms as $platform) {
            $file = $platform['file'];
            $filename = $file->getClientOriginalName();
            
            // Store file in public/releases directory
            $filePath = $file->storeAs('releases', $filename, 'public');
            
            $platforms[$platform['name']] = [
                'signature' => $platform['signature'] ?? null,
                'url' => asset('storage/' . $filePath)
            ];
        }
        
        $data['platforms'] = $platforms;

        // If this is marked as latest, unmark all other versions as latest
        if ($request->is_latest) {
            SoftwareUpdate::where('target', $request->target)
                         ->update(['is_latest' => false]);
        }

        // Deactivate previous versions for the same target
        SoftwareUpdate::where('target', $request->target)
                     ->update(['is_active' => false]);

        $update = SoftwareUpdate::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Update created successfully',
            'update' => $update
        ]);
    }

    /**
     * Show edit update form
     */
    public function edit(SoftwareUpdate $update)
    {
        return view('admin.updates.edit', compact('update'));
    }

    /**
     * Update existing update
     */
    public function update(Request $request, SoftwareUpdate $update)
    {
        $validator = Validator::make($request->all(), [
            'target' => 'required|string|max:255',
            'version' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'pub_date' => 'required|date',
            'is_active' => 'boolean',
            'is_latest' => 'boolean',
            'download_url' => 'nullable|string|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['target', 'version', 'notes', 'pub_date', 'is_active', 'is_latest', 'download_url']);
        $data['pub_date'] = \Carbon\Carbon::parse($request->pub_date);

        // If this is marked as latest, unmark all other versions as latest
        if ($request->is_latest) {
            SoftwareUpdate::where('target', $request->target)
                         ->where('id', '!=', $update->id)
                         ->update(['is_latest' => false]);
        }

        $update->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Update updated successfully',
            'update' => $update
        ]);
    }

    /**
     * Delete update
     */
    public function destroy(SoftwareUpdate $update)
    {
        // Delete associated files
        foreach ($update->platforms as $platform) {
            if (isset($platform['url'])) {
                $filePath = str_replace(asset('storage/'), '', $platform['url']);
                Storage::disk('public')->delete($filePath);
            }
        }

        $update->delete();

        return response()->json([
            'success' => true,
            'message' => 'Update deleted successfully'
        ]);
    }

    /**
     * Toggle update active status
     */
    public function toggleActive(SoftwareUpdate $update)
    {
        $update->update(['is_active' => !$update->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Update status updated successfully',
            'update' => $update
        ]);
    }

    /**
     * Set update as latest version
     */
    public function setLatest(SoftwareUpdate $update)
    {
        // Unmark all other versions as latest for the same target
        SoftwareUpdate::where('target', $update->target)
                     ->where('id', '!=', $update->id)
                     ->update(['is_latest' => false]);

        // Mark this version as latest
        $update->update(['is_latest' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Version set as latest successfully',
            'update' => $update
        ]);
    }

    /**
     * Get all updates (AJAX)
     */
    public function getUpdates()
    {
        $updates = SoftwareUpdate::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'updates' => $updates
        ]);
    }
}
