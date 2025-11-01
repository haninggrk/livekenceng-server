<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberSubscription extends Model
{
    protected $fillable = [
        'member_id',
        'app_id',
        'machine_id',
        'expiry_date',
        'last_login_at',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Member who owns this subscription
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * App this subscription is for
     */
    public function app()
    {
        return $this->belongsTo(App::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        
        return $this->expiry_date->isFuture();
    }

    /**
     * Check if machine ID matches
     */
    public function checkMachineId(string $machineId): bool
    {
        // If no machine ID is set, it's a new subscription
        if (!$this->machine_id) {
            return true;
        }
        
        return $this->machine_id === $machineId;
    }
}
