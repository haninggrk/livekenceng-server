<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\App;
use App\Models\Member;
use App\Models\MemberSubscription;
use App\Models\LicenseKey;
use App\Models\LicensePlan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create default app for backward compatibility
        $defaultApp = App::create([
            'name' => 'livekenceng',
            'display_name' => 'Livekenceng Shopee App',
            'identifier' => 'livekenceng',
            'description' => 'Default Livekenceng Shopee Automation App',
            'is_active' => true,
        ]);

        // Migrate existing members to subscriptions
        $members = Member::all();
        foreach ($members as $member) {
            if ($member->machine_id || $member->expiry_date) {
                MemberSubscription::create([
                    'member_id' => $member->id,
                    'app_id' => $defaultApp->id,
                    'machine_id' => $member->machine_id,
                    'expiry_date' => $member->expiry_date,
                    'created_at' => $member->created_at,
                    'updated_at' => $member->updated_at,
                ]);
            }
        }

        // Migrate existing license keys
        LicenseKey::whereNull('app_id')->update(['app_id' => $defaultApp->id]);

        // Migrate existing license plans
        LicensePlan::whereNull('app_id')->update(['app_id' => $defaultApp->id]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration cannot be fully reversed as it would lose data
        // The reverse would require recreating member data from subscriptions
        // For safety, we'll just remove the default app
        
        // Find and delete default app (this will cascade delete subscriptions)
        $defaultApp = App::where('identifier', 'livekenceng')->first();
        if ($defaultApp) {
            $defaultApp->delete();
        }
        
        // Set all related records to null
        LicenseKey::whereNotNull('app_id')->update(['app_id' => null]);
        LicensePlan::whereNotNull('app_id')->update(['app_id' => null]);
    }
};
