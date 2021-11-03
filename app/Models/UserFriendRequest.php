<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "user_friend_requests".
 *
 * @property int $medium
 *
 * @property int $id
 * @property int $user_id
 * @property int $friend_user_id
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property \App\User $user
 * @property \App\User $friend
 * @property \App\Models\FriendRequestMedium[] $mediums
 */
class UserFriendRequest extends Model
{
    private $_medium;

    use SoftDeletes;

    const STATUS_PENDING = 0;
    const STATUS_ACCEPTED = 10;
    const STATUS_REJECTED = 20;

    public static $STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACCEPTED => 'Accepted',
        self::STATUS_REJECTED => 'Rejected',
    ];

    protected $fillable = ['user_id','friend_user_id','status','medium']; // $this->medium is used to pass value to observer.
    protected $visible = ['id','user_id','friend_user_id','status','status_text','request_mediums', 'user_details','created_at','updated_at'];
    protected $attributes = ['status'=>self::STATUS_PENDING];
    protected $appends = ['status_text','request_mediums', 'user_details'];

    // friend request has a user
    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    // friend request has a friend user
    public function friend()
    {
        return $this->belongsTo('App\User','friend_user_id');
    }

    public function mediums(){
        return $this->hasMany('App\Models\FriendRequestMedium', 'friend_requests_id');
    }

    public function getStatusTextAttribute()
    {
        return self::$STATUSES[$this->status];
    }
    public function getRequestMediumsAttribute()
    {
        return $this->mediums;
    }
    public function getUserDetailsAttribute()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getMediumAttribute()
    {
        return $this->_medium;
    }

    /**
     * @param mixed $medium
     */
    public function setMediumAttribute($medium)
    {
        $this->_medium = $medium;
    }

    /**
     * Scope a query to only include friend requests that are still on pending state.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendingOnly($query){
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include friend requests that are sent to a particular user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSentTo($query, $userId){
        return $query->where('friend_user_id', $userId);
    }
}
