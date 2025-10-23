<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareUpdate extends Model
{
    protected $fillable = [
        'target',
        'version',
        'notes',
        'pub_date',
        'platforms',
        'is_active'
    ];

    protected $casts = [
        'pub_date' => 'datetime',
        'platforms' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the latest active update for a specific target
     */
    public static function getLatestUpdate($target)
    {
        return static::where('target', $target)
                    ->where('is_active', true)
                    ->orderBy('pub_date', 'desc')
                    ->first();
    }

    /**
     * Check if a version is newer than the current version
     */
    public static function isNewerVersion($target, $currentVersion)
    {
        $latestUpdate = static::getLatestUpdate($target);
        
        if (!$latestUpdate) {
            return false;
        }

        return version_compare($latestUpdate->version, $currentVersion, '>');
    }
}
