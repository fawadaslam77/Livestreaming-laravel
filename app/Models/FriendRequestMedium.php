<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "friend_request_medium".
 *
 * @property int $id
 * @property int $friend_requests_id
 * @property int $medium
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\UserFriendRequest $friendRequest
 */
class FriendRequestMedium extends Model
{
    protected $table = 'friend_request_mediums';
    use SoftDeletes;

    const MEDIUM_APP_REQUEST = 0;
    const MEDIUM_PHONEBOOK = 10;
    const MEDIUM_EMAIL = 20;
    const MEDIUM_FACEBOOK = 30;
    const MEDIUM_TWITTER = 40;
    const MEDIUM_GOOGLE_PLUS = 50;

    public static $MEDIUMS = [
        self::MEDIUM_APP_REQUEST => 'APP Request',
        self::MEDIUM_PHONEBOOK => 'Phonebook',
        self::MEDIUM_EMAIL => 'Email',
        self::MEDIUM_FACEBOOK => 'Facebook',
        self::MEDIUM_TWITTER => 'Twitter',
        self::MEDIUM_GOOGLE_PLUS => 'Google+',
    ];

    protected $fillable = ['friend_requests_id', 'medium'];
    protected $visible = ['friend_requests_id', 'medium','created_at','updated_at',
        'medium_text'
    ];
    protected $attributes = ['medium'=>self::MEDIUM_APP_REQUEST];
    protected $appends = ['medium_text'];

    // friend mediums has a friend
    public function friendRequest()
    {
        return $this->belongsTo('App\Models\UserFriendRequest','friend_requests_id');
    }

    public function getMediumTextAttribute(){
        return self::$MEDIUMS[$this->medium];
    }
}
