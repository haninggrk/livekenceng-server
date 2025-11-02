<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'email',
        'password',
        'machine_id',
        'expiry_date',
        'telegram_username',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
    ];

    /**
     * Check if member subscription is active
     */
    public function isActive(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        
        return $this->expiry_date->isFuture();
    }

    /**
     * License keys used by this member
     */
    public function licenseKeys()
    {
        return $this->hasMany(LicenseKey::class, 'used_by');
    }

    /**
     * Shopee accounts owned by this member
     */
    public function shopeeAccounts()
    {
        return $this->hasMany(ShopeeAccount::class);
    }

    /**
     * Active Shopee accounts owned by this member
     */
    public function activeShopeeAccounts()
    {
        return $this->hasMany(ShopeeAccount::class)->where('is_active', true);
    }

    /**
     * Subscriptions for different apps
     */
    public function subscriptions()
    {
        return $this->hasMany(MemberSubscription::class);
    }

    /**
     * Active subscriptions
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(MemberSubscription::class)->where('expiry_date', '>', now());
    }

    /**
     * Niches owned by this member
     */
    public function niches()
    {
        return $this->hasMany(Niche::class);
    }

    /**
     * Product sets owned by this member
     */
    public function productSets()
    {
        return $this->hasMany(ProductSet::class);
    }

    /**
     * Device metadata owned by this member
     */
    public function deviceMetadata()
    {
        return $this->hasMany(DeviceMetadata::class);
    }

    /**
     * Check if member has active subscription for a specific app
     */
    public function hasActiveSubscriptionForApp($appId): bool
    {
        $subscription = $this->subscriptions()->where('app_id', $appId)->first();
        return $subscription && $subscription->isActive();
    }

    /**
     * Get subscription for a specific app
     */
    public function getSubscriptionForApp($appId)
    {
        return $this->subscriptions()->where('app_id', $appId)->first();
    }

    /**
     * Check if member is expired and update machine_id if needed
     */
    public function checkAndUpdateExpiredStatus(): bool
    {
        if (!$this->isActive() && $this->machine_id && $this->machine_id !== 'EXPIRED') {
            $this->machine_id = 'EXPIRED';
            $this->save();
            return true; // Status was updated
        }
        return false; // No update needed
    }
}
