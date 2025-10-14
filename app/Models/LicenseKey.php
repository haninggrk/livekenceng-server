<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LicenseKey extends Model
{
    protected $fillable = [
        'code',
        'duration_days',
        'plan_id',
        'price',
        'is_used',
        'used_by',
        'created_by',
        'reseller_id',
        'used_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    /**
     * Generate a unique license key
     */
    public static function generateCode(): string
    {
        do {
            $code = 'LK-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Member who used this license
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'used_by');
    }

    /**
     * Admin who created this license
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Reseller who generated this license
     */
    public function reseller()
    {
        return $this->belongsTo(Reseller::class, 'reseller_id');
    }

    /**
     * Plan associated with this license (optional)
     */
    public function plan()
    {
        return $this->belongsTo(LicensePlan::class, 'plan_id');
    }
}
