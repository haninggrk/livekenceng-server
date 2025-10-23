<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopeeAccount extends Model
{
    protected $fillable = [
        'member_id',
        'name',
        'cookie',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Member who owns this Shopee account
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
