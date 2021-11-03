<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "packages".
 *
 * @property int $id
 * @property string $name
 * @property int $daily_limit
 * @property int $storage_limit
 * @property int $expire_days
 * @property int $dashboard
 * @property int $allow_240
 * @property int $allow_480
 * @property int $allow_720
 * @property int $allow_1080
 * @property int $allow_save_offline
 * @property int $disable_ads
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\UserPackage[] $userPackages
 */
class Package extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'daily_limit',
        'storage_limit',
        'expire_days',
        'dashboard',
        'allow_240',
        'allow_480',
        'allow_720',
        'allow_1080',
        'allow_save_offline',
        'disable_ads',
    ];

    protected $visible = [
        'name',
        'daily_limit',
        'storage_limit',
        'expire_days',
        'dashboard',
        'allow_240',
        'allow_480',
        'allow_720',
        'allow_1080',
        'allow_save_offline',
        'disable_ads',
        'created_at',
        'update_at',
    ];
    // package has many user packages
    public function userPackages()
    {
        return $this->hasMany('App\Models\UserPackage','package_id');
    }
}
