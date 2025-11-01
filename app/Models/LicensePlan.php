<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicensePlan extends Model
{
    protected $fillable = [
        'name',
        'duration_days',
        'app_id',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function licenseKeys()
    {
        return $this->hasMany(LicenseKey::class, 'plan_id');
    }

    /**
     * App this plan belongs to
     */
    public function app()
    {
        return $this->belongsTo(App::class);
    }
}
