<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\DeviceMetadata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DeviceMetadataController extends Controller
{
    /**
     * Get all device metadata for authenticated member
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

        $devices = $member->deviceMetadata()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'devices' => $devices
        ]);
    }

    /**
     * Get a single device metadata
     */
    public function show(Request $request, DeviceMetadata $deviceMetadata)
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

        if ($deviceMetadata->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Device metadata not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'device' => $deviceMetadata
        ]);
    }

    /**
     * Create a new device metadata
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'manufacturer' => 'nullable|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'device_model' => 'nullable|string|max:255',
            'rom' => 'nullable|string|max:255',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $device = DeviceMetadata::create([
            'member_id' => $member->id,
            'manufacturer' => $request->manufacturer,
            'device_name' => $request->device_name,
            'device_model' => $request->device_model,
            'rom' => $request->rom,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device metadata created successfully',
            'device' => $device
        ], 201);
    }

    /**
     * Update a device metadata
     */
    public function update(Request $request, DeviceMetadata $deviceMetadata)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'manufacturer' => 'nullable|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'device_model' => 'nullable|string|max:255',
            'rom' => 'nullable|string|max:255',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($deviceMetadata->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Device metadata not found'
            ], 404);
        }

        $deviceMetadata->update([
            'manufacturer' => $request->input('manufacturer', $deviceMetadata->manufacturer),
            'device_name' => $request->input('device_name', $deviceMetadata->device_name),
            'device_model' => $request->input('device_model', $deviceMetadata->device_model),
            'rom' => $request->input('rom', $deviceMetadata->rom),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device metadata updated successfully',
            'device' => $deviceMetadata->fresh()
        ]);
    }

    /**
     * Delete a device metadata
     */
    public function destroy(Request $request, DeviceMetadata $deviceMetadata)
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

        if ($deviceMetadata->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Device metadata not found'
            ], 404);
        }

        $deviceMetadata->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device metadata deleted successfully'
        ]);
    }
}
