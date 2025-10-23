<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SoftwareUpdate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    /**
     * Check for software updates
     * GET /api/updates/{target}/{current_version}
     */
    public function checkUpdate(Request $request, string $target, string $currentVersion): JsonResponse
    {
        // Get the latest update for this target
        $latestUpdate = SoftwareUpdate::getLatestUpdate($target);
        
        if (!$latestUpdate) {
            return response()->json([
                'error' => 'No updates found for target: ' . $target
            ], 404);
        }

        // Check if the latest version is newer than current
        if (!SoftwareUpdate::isNewerVersion($target, $currentVersion)) {
            return response()->json([
                'message' => 'No updates available',
                'current_version' => $currentVersion,
                'latest_version' => $latestUpdate->version
            ], 200);
        }

        // Return update information in the expected format
        return response()->json([
            'version' => $latestUpdate->version,
            'notes' => $latestUpdate->notes ?? 'Bug fixes and improvements',
            'pub_date' => $latestUpdate->pub_date->toISOString(),
            'platforms' => $latestUpdate->platforms
        ]);
    }

    /**
     * Get update information without version comparison
     * GET /api/updates/{target}
     */
    public function getUpdateInfo(string $target): JsonResponse
    {
        $latestUpdate = SoftwareUpdate::getLatestUpdate($target);
        
        if (!$latestUpdate) {
            return response()->json([
                'error' => 'No updates found for target: ' . $target
            ], 404);
        }

        return response()->json([
            'version' => $latestUpdate->version,
            'notes' => $latestUpdate->notes ?? 'Bug fixes and improvements',
            'pub_date' => $latestUpdate->pub_date->toISOString(),
            'platforms' => $latestUpdate->platforms
        ]);
    }
}
