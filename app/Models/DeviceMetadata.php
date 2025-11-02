<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceMetadata extends Model
{
    protected $fillable = [
        'member_id',
        'manufacturer',
        'device_name',
        'device_model',
        'rom',
    ];

    /**
     * Member that owns this device metadata
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
