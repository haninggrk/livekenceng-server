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
            'platforms' => 'required|array|min:1',
            'platforms.*.name' => 'required_with:platforms.*.file|string',
            'platforms.*.signature' => 'nullable|string',
            'platforms.*.file' => 'required_with:platforms.*.name|file|max:102400', // 100MB max, any file type
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
            // Only process platforms that have both name and file
            if (isset($platform['name']) && isset($platform['file']) && $platform['file']->isValid()) {
                $file = $platform['file'];
                $fileExtension = $file->getClientOriginalExtension();
                
                // Create uniform filename: target-version.filetype
                $uniformFilename = $request->target . '-' . $request->version . '.' . $fileExtension;
                
                // Store file with uniform name
                $filePath = $file->storeAs('releases', $uniformFilename, 'public');
                
                $platforms[$platform['name']] = [
                    'signature' => $platform['signature'] ?? null,
                    'url' => asset('storage/' . $filePath)
                ];
            }
        }
        
        $data['platforms'] = $platforms;

        // If this is marked as latest, unmark all other versions as latest
        if ($request->is_latest) {
            SoftwareUpdate::where('target', $request->target)
                         ->update(['is_latest' => false]);
        }

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
            'new_platforms' => 'nullable|array',
            'new_platforms.*.name' => 'required_with:new_platforms.*.file|string',
            'new_platforms.*.signature' => 'nullable|string',
            'new_platforms.*.file' => 'required_with:new_platforms.*.name|file|max:102400',
            'platforms_to_delete' => 'nullable|array',
            'platforms_to_delete.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['target', 'version', 'notes', 'pub_date', 'is_active', 'is_latest', 'download_url']);
        $data['pub_date'] = \Carbon\Carbon::parse($request->pub_date);

        // Handle platform deletions and new platform uploads
        $existingPlatforms = $update->platforms;
        
        // Delete platforms marked for deletion
        if ($request->has('platforms_to_delete')) {
            foreach ($request->platforms_to_delete as $platformToDelete) {
                if (isset($existingPlatforms[$platformToDelete])) {
                    // Delete the file from storage
                    $platformData = $existingPlatforms[$platformToDelete];
                    if (isset($platformData['url'])) {
                        $filePath = str_replace(asset('storage/'), '', $platformData['url']);
                        Storage::disk('public')->delete($filePath);
                    }
                    
                    // Remove from platforms array
                    unset($existingPlatforms[$platformToDelete]);
                }
            }
        }
        
        // Handle new platform uploads
        if ($request->has('new_platforms')) {
            foreach ($request->new_platforms as $platform) {
                if (isset($platform['name']) && isset($platform['file']) && $platform['file']->isValid()) {
                    $file = $platform['file'];
                    $fileExtension = $file->getClientOriginalExtension();
                    
                    // Create uniform filename: target-version.filetype
                    $uniformFilename = $request->target . '-' . $request->version . '.' . $fileExtension;
                    
                    // Store file with uniform name
                    $filePath = $file->storeAs('releases', $uniformFilename, 'public');
                    
                    $existingPlatforms[$platform['name']] = [
                        'signature' => $platform['signature'] ?? null,
                        'url' => asset('storage/' . $filePath)
                    ];
                }
            }
        }
        
        // Update platforms data if there were changes
        if ($request->has('platforms_to_delete') || $request->has('new_platforms')) {
            $data['platforms'] = $existingPlatforms;
        }

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
