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
