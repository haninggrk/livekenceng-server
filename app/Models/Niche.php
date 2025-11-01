<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Niche extends Model
{
    protected $fillable = [
        'member_id',
        'name',
        'description',
    ];

    /**
     * Member who owns this niche
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Product sets in this niche
     */
    public function productSets()
    {
        return $this->hasMany(ProductSet::class);
    }
}
