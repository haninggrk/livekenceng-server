<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSetItem extends Model
{
    protected $fillable = [
        'product_set_id',
        'url',
        'shop_id',
        'item_id',
    ];

    /**
     * Product set this item belongs to
     */
    public function productSet()
    {
        return $this->belongsTo(ProductSet::class);
    }
}
