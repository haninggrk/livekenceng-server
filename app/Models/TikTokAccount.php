<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TikTokAccount extends Model
{
    protected $table = 'tiktok_accounts';

    protected $fillable = [
        'member_id',
        'name',
        'email',
        'username',
        'cookie',
    ];

    /**
     * Member who owns this TikTok account
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
