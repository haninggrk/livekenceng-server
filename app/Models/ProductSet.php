<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSet extends Model
{
    protected $fillable = [
        'member_id',
        'niche_id',
        'name',
        'description',
    ];

    /**
     * Member who owns this product set
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Niche this product set belongs to (optional)
     */
    public function niche()
    {
        return $this->belongsTo(Niche::class);
    }

    /**
     * Items in this product set
     */
    public function items()
    {
        return $this->hasMany(ProductSetItem::class);
    }
}
