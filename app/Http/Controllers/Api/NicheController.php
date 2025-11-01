<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Niche;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class NicheController extends Controller
{
    /**
     * Get all niches for authenticated member
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

        $niches = $member->niches()->with('productSets')->get();

        return response()->json([
            'success' => true,
            'niches' => $niches
        ]);
    }

    /**
     * Get a single niche
     */
    public function show(Request $request, Niche $niche)
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

        if ($niche->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Niche not found'
            ], 404);
        }

        $niche->load('productSets.items');

        return response()->json([
            'success' => true,
            'niche' => $niche
        ]);
    }

    /**
     * Create a new niche
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $niche = Niche::create([
            'member_id' => $member->id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Niche created successfully',
            'niche' => $niche
        ], 201);
    }

    /**
     * Update a niche
     */
    public function update(Request $request, Niche $niche)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($niche->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Niche not found'
            ], 404);
        }

        $niche->update([
            'name' => $request->input('name', $niche->name),
            'description' => $request->input('description', $niche->description),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Niche updated successfully',
            'niche' => $niche->fresh()
        ]);
    }

    /**
     * Delete a niche
     */
    public function destroy(Request $request, Niche $niche)
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

        if ($niche->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Niche not found'
            ], 404);
        }

        $niche->delete();

        return response()->json([
            'success' => true,
            'message' => 'Niche deleted successfully'
        ]);
    }
}
