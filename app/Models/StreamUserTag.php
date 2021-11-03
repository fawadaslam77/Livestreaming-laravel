<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "stream_user_tags".
 *
 * @property int $id
 * @property int $user_id
 * @property int $stream_id
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property \App\User $user
 * @property \App\Models\UserStream $stream
 */
class StreamUserTag extends Model
{
    use SoftDeletes;

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 10;
    const STATUS_DECLINED = 20;

    public static $STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_DECLINED => 'Declined',
    ];

    protected $fillable = ['user_id','stream_id','status'];
    protected $visible = ['user_id','stream_id','status','created_at','updated_at',
        'status_text'
    ];
    protected $attributes = ['status'=>self::STATUS_PENDING];
    protected $appends = ['status_text'];
    // stream user tag has a user
    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    // stream user tag has a stream
    public function stream()
    {
        return $this->belongsTo('App\Models\UserStream','stream_id');
    }

    public function getStatusTextAttribute()
    {
        return self::$STATUSES[$this->status];
    }
}
