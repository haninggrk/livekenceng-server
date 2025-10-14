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
}
