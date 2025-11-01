<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'identifier',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Subscriptions for this app
     */
    public function subscriptions()
    {
        return $this->hasMany(MemberSubscription::class);
    }

    /**
     * License keys for this app
     */
    public function licenseKeys()
    {
        return $this->hasMany(LicenseKey::class);
    }

    /**
     * License plans for this app
     */
    public function licensePlans()
    {
        return $this->hasMany(LicensePlan::class);
    }

    /**
     * Active license plans for this app
     */
    public function activeLicensePlans()
    {
        return $this->hasMany(LicensePlan::class)->where('is_active', true);
    }
}
