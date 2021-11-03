<?php

namespace App\Models;

use Config;
use function GuzzleHttp\Psr7\str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

/**
 * This is the model class for table "stream_user_actions".
 *
 * @property int $id
 * @property int $user_id
 * @property int $stream_id
 * @property int $type
 * @property string $details
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property \App\User $user
 * @property \App\Models\UserStream $stream
 */
class StreamUserAction extends Model
{
    use SoftDeletes;

    const TYPE_BLOCK       = 0;
    const TYPE_REPORT      = 10;
    const TYPE_FAVORITE    = 20;
    const TYPE_WATCH_LATER = 30;
    const TYPE_SAVE        = 40;
    const TYPE_SHARE       = 50;
    const TYPE_LIKE        = 60;
    const TYPE_DISLIKE     = 70;
    const TYPE_VIEW        = 80;

    public static $TYPES = [
        self::TYPE_BLOCK => 'Block',
        self::TYPE_REPORT => 'Report',
        self::TYPE_FAVORITE => 'Favorite',
        self::TYPE_WATCH_LATER => 'Watch Later',
        self::TYPE_SAVE => 'Save',
        self::TYPE_SHARE => 'Share',
        self::TYPE_LIKE => 'Like',
        self::TYPE_DISLIKE => 'Dislike',
        self::TYPE_VIEW => 'View',
    ];

    protected $fillable = ['user_id','stream_id','type','details'];
    protected $visible = ['id','user_id','stream_id','type','details','created_at','updated_at',
        'type_text','stream_details'
    ];
    protected $attributes = ['type'=>self::TYPE_BLOCK];
    protected $appends = ['type_text','stream_details'];
    // stream user action has a user
    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    // stream user action has a stream
    public function stream()
    {
        return $this->belongsTo('App\Models\UserStream','stream_id');
    }

    public function getTypeTextAttribute(){
        return self::$TYPES[$this->type];
    }
    public function getStreamDetailsAttribute(){
        return $this->stream;
    }

    /**
     * Scope a query to only include actions that are done by a particular user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query,$user_id){
        return $query->where('user_id',$user_id);
    }

    public static function is_already($stream_id,$user_id,$type){
        $is_already= DB::table('stream_user_actions')
            ->where("stream_id",'=',@$stream_id)
            ->where("user_id",'=',$user_id)
            ->where("type",'=',$type)
            ->count();
        return $is_already;
    }
    /**
     * Scope a query to only include actions by particular type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query,$type){
        return $query->where('type',$type);
    }
}
