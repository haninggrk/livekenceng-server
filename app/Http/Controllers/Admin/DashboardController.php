<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\LicenseKey;
use App\Models\Reseller;
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
        
        return view('admin.dashboard', compact('members', 'licenseKeys', 'resellers'));
    }

    /**
     * Get all members (AJAX)
     */
    public function getMembers()
    {
        $members = Member::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'members' => $members
        ]);
    }

    /**
     * Create new member
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:members,email',
            'password' => 'required|min:6',
            'expiry_date' => 'nullable|date',
        ]);

        $member = Member::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
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
            'expiry_date' => 'nullable|date',
        ]);

        $member->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $member->password = Hash::make($validated['password']);
        }

        if (isset($validated['machine_id'])) {
            $member->machine_id = $validated['machine_id'];
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
            'duration_days' => 'required|in:1,3,7,14,30',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $licenses = [];
        for ($i = 0; $i < $validated['quantity']; $i++) {
            $license = LicenseKey::create([
                'code' => LicenseKey::generateCode(),
                'duration_days' => $validated['duration_days'],
                'created_by' => auth()->user()->id,
            ]);
            $licenses[] = $license;
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

        // Store prices in session or database
        // For simplicity, we'll use session for now
        $prices = session('license_prices', [
            1 => 10000,
            3 => 25000,
            7 => 40000,
            14 => 70000,
            30 => 139000,
        ]);

        $prices[$validated['duration_days']] = $validated['price'];
        session(['license_prices' => $prices]);

        return response()->json([
            'success' => true,
            'message' => 'License price updated successfully',
            'prices' => $prices
        ]);
    }
}
