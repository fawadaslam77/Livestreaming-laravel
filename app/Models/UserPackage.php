<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "user_packages".
 *
 * @property int $id
 * @property int $user_id
 * @property int $package_id
 * @property float $amount
 * @property int $payment_status
 * @property string $payment_response
 * @property string $expire_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property \App\User $user
 * @property \App\Models\StreamCategory[] $categories
 */
class UserPackage extends Model
{
    use SoftDeletes;

    const PAYMENT_STATUS_CANCELLED = 0;
    const PAYMENT_STATUS_PENDING = 10;
    const PAYMENT_STATUS_APPROVED = 20;

    public static $STATUSES = [
        self::PAYMENT_STATUS_CANCELLED => 'Cancelled',
        self::PAYMENT_STATUS_PENDING => 'Pending',
        self::PAYMENT_STATUS_APPROVED => 'Approved',
    ];

    protected $fillable = ['user_id','package_id','amount','payment_status','payment_response','expire_at'];
    protected $visible = ['user_id','package_id','amount','payment_status','payment_response','expire_at','created_at','updated_at',
        'status_text'
    ];
    protected $attributes = ['payment_status'=>self::PAYMENT_STATUS_PENDING];
    protected $appends = ['status_text'];

    // user package has a package
    public function package()
    {
        return $this->belongsTo('App\Models\Package','package_id');
    }

    // user package has a user
    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function getStatusTextAttribute()
    {
        return self::$STATUSES[$this->payment_status];
    }
}
