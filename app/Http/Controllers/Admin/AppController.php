<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\App;
use Illuminate\Http\Request;

class AppController extends Controller
{
    /**
     * Get all apps
     */
    public function index()
    {
        $apps = App::withCount(['subscriptions', 'licenseKeys', 'licensePlans'])
                   ->orderBy('created_at', 'desc')
                   ->get();

        return response()->json([
            'success' => true,
            'apps' => $apps
        ]);
    }

    /**
     * Get a single app
     */
    public function show(App $app)
    {
        return response()->json([
            'success' => true,
            'app' => $app
        ]);
    }

    /**
     * Store a new app
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:apps,name',
            'display_name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:apps,identifier',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $app = App::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'App created successfully',
            'app' => $app
        ]);
    }

    /**
     * Update an app
     */
    public function update(Request $request, App $app)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:apps,name,' . $app->id,
            'display_name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:apps,identifier,' . $app->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $app->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'App updated successfully',
            'app' => $app
        ]);
    }

    /**
     * Delete an app
     */
    public function destroy(App $app)
    {
        $app->delete();

        return response()->json([
            'success' => true,
            'message' => 'App deleted successfully'
        ]);
    }

    /**
     * Toggle app active status
     */
    public function toggleActive(App $app)
    {
        $app->is_active = !$app->is_active;
        $app->save();

        return response()->json([
            'success' => true,
            'message' => 'App status updated successfully',
            'app' => $app
        ]);
    }
}
