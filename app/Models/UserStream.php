<?php

namespace App\Models;

use App\User;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\Friend;
use App\Models\UserFriendRequest;
/**
 * This is the model class for table "user_streams".
 *
 * @property int $id
 * @property int $user_id
 * @property string $uuid
 * @property string $name
 * @property string $stream_ip
 * @property string $stream_port
 * @property string $stream_app
 * @property int $status
 * @property int $privacy_setting
 * @property int $quality
 * @property int $is_public
 * @property int $allow_comments
 * @property int $allow_tag_requests
 * @property int $available_later
 * @property float $lng
 * @property float $lat
 * @property string $start_time
 * @property int $total_likes
 * @property int $total_dislikes
 * @property int $total_shares
 * @property int $total_viewers
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $streamUsername
 * @property string $streamPassword
 *
 * @property \App\User $user
 * @property \App\Models\StreamCategory[] $categories
 * @property \App\Models\StreamUserTag[] $tags
 * @property \App\Models\StreamUserAction[] $actions
 */
class UserStream extends Model
{

    use SoftDeletes;

    const QUALITY_240   = 240;
    const QUALITY_480   = 480;
    const QUALITY_720   = 720;
    const QUALITY_1080  = 1080;

    public static $QUALITIES = [
        self::QUALITY_240 => '240p',
        self::QUALITY_480 => '480p',
        self::QUALITY_720 => '720p',
        self::QUALITY_1080 => '1080p',
    ];

    const STATUS_WAITING = 0;
    const STATUS_LIVE = 10;
    const STATUS_ENDED = 20;

    public static $STATUSES = [
        self::STATUS_WAITING => 'Waiting for user to go live',
        self::STATUS_LIVE => 'Live',
        self::STATUS_ENDED => 'Ended',
    ];

    // Mass Assignable
    protected $fillable = ['user_id','name','quality','is_public','allow_comments','allow_tag_requests', 'available_later', 'lng','lat'];
    // 'uuid', 'privacy_setting', // Will be assigned by observers
    // 'status',  // will have default value then have a end-stream route to update the value

    // BELOW fields: Starts with 0 and will be updated as per event occurs.
    // 'total_likes','total_dislikes','total_shares','total_viewers' // have their own routes to update values


    protected $visible = ['id','user_id','uuid','name','stream_ip','stream_port','stream_app','status','privacy_setting','quality','is_public','allow_comments','allow_tag_requests', 'available_later', 'lng','lat','total_likes','total_dislikes','total_shares','total_viewers','created_at','updated_at','streamUsername','streamPassword',
        'status_text', 'quality_text', 'user_details'
    ]; // 'start_time',

    protected $attributes = [
        'status' => self::STATUS_LIVE,
        'quality' => self::QUALITY_480,
        'is_public' => 1,
        'allow_comments' => 1,
        'allow_tag_requests' => 1,
        'available_later' => 1,
        'total_likes' => 0,
        'total_dislikes' => 0,
        'total_shares' => 0,
        'total_viewers' => 0,
    ];

    protected $appends = ['status_text', 'quality_text','user_details'];

    // user stream has a user

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    // stream has many categories
    public function categories()
    {
        return $this->belongsToMany('App\Models\StreamCategory', 'user_stream_categories');
    }

    // stream has many tags - (Accepted Requests / Pending Requests / Rejected Requests)
    public function tags(){
        return $this->hasMany('App\Models\StreamUserTag', 'stream_id');
    }

    // stream has many actions - (BLOCK, REPORT, FAVORITE, WATCH_LATER, SAVE, SHARE, LIKE, DISLIKE)
    public function actions(){
        return $this->hasMany('App\Models\StreamUserAction', 'stream_id');
    }

    // stream has many actions - (BLOCK, REPORT, FAVORITE, WATCH_LATER, SAVE, SHARE, LIKE, DISLIKE)
    public function sharedByFriendOrFollower(){
        return $this->hasMany('App\Models\Friend', 'friend_user_id', 'user_id');
    }

    public function getUserDetailsAttribute()
    {
        return ($this->user) ? $this->user : [];
    }

    public function getStatusTextAttribute()
    {
        return self::$STATUSES[$this->status];
    }

    public function getQualityTextAttribute()
    {
        return self::$QUALITIES[$this->quality];
    }

    public function getStreamUsernameAttribute()
    {
        return ($this->user) ? $this->user->username : "default-user";
    }

    public function getStreamPasswordAttribute()
    {
        return base64_encode($this->streamUsername.'@'. \Config::get('constants.global.site.domain'));
    }

    /**
     * Scope a query to only include streams that have is_public=1.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublicOnly($query){
        return $query->where('is_public', 1);
    }

    /**
     * Scope a query to order by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByStatus($query){
        return $query->orderBy('status');
    }

    /**
     * Scope a query to order by popular.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopular($query){
        return $query->orderBy(DB::Raw("((`total_likes` + `total_shares`) - `total_dislikes`)"), "DESC");
    }

    /**
     * Scope a query to only include streams that have is_public=1.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLiveOrAvailableLater($query){
        return $query->where('status', self::STATUS_LIVE)->orWhere([
            'status'=>self::STATUS_ENDED,
            'available_later'=>1,
        ]);
    }

    /**
     * Scope a query to only include streams that have is_public=1.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSharedByFriendOrFollowed($query , $userId){
        return $query->leftJoin('friends', function($join) use($userId) {
            $join->on('friends.friend_user_id', '=', 'user_streams.user_id');
            $join->on('friends.user_id', '=', DB::Raw("'$userId'"));
        })->orderBy('friends.type', 'asc');
    }

    /**
     * Scope a query to only include streams that are shared to the authenticated user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrivateStreamsSharedTo($query, $userid){
        return $query->orWhere([
            'is_public'=>0,
            [
                function($query) use($userid) {
                    return $query->where(function($query) {
                        return $query->whereIn('privacy_setting', [User::PRIVACY_FRIENDS_ONLY, User::PRIVACY_FRIENDS_FOLLOWERS])->where('friends.type' , Friend::TYPE_FRIEND);
                    })->orWhere(function($query) {
                        return $query->whereIn('privacy_setting', [User::PRIVACY_FOLLOWERS_ONLY, User::PRIVACY_FRIENDS_FOLLOWERS])->where('friends.type' , Friend::TYPE_FOLLOW);
                    })->orWhere([
                        'privacy_setting'=> User::PRIVACY_EVERYONE,
                        ['friends.type' ,'<>', Friend::TYPE_BLOCK],
                    ]);
                }
            ]
        ]);
    }

    /**
     * Scope a query to only include streams that are not blocked by the authenticated user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotBlockedByUser($query, $userId){
        /*return $query->whereNotIn('user_streams.id',// [100]
            function($query) use($userId) {
                return $query->select('stream_user_actions.stream_id')->from((new StreamUserAction())->getTable())->where(['user_id'=>$userId,'type'=>StreamUserAction::TYPE_BLOCK, 'stream_id'=>DB::Raw('user_streams.id')]);
            }
        );*/
        return $query->leftJoin('stream_user_actions', function($join) use($userId) {
            $join->on('stream_user_actions.stream_id', '=', 'user_streams.id');
            $join->on('stream_user_actions.user_id', '=', DB::Raw("'$userId'"));
        })->where('stream_user_actions.type', '<>', StreamUserAction::TYPE_BLOCK)->orWhere('stream_user_actions.type',null);
    }

    /**
     * Order by Created At.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestFirst($query){
        return $query->orderBy('user_streams.created_at','desc');
    }

    /**
     * Scope a query to only include streams that marked $type by the authenticated user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByActionType($query, $type, $userId){
        return $query->join('stream_user_actions',function($join) use($userId,$type) {
            $join->on('stream_user_actions.stream_id', '=', 'user_streams.id');
            $join->on('stream_user_actions.user_id', '=', DB::Raw("'$userId'"));
            $join->on('stream_user_actions.type', '=', DB::Raw("'$type'"));
        })->whereNull('stream_user_actions.deleted_at')->orderBy('stream_user_actions.created_at', 'desc')->select('user_streams.*');
    }

    //TODO: ADD SCOPE FOR USER SELECTED CATEGORY
    //TODO: ADD SCOPE FOR NEARBY LOCATION

    protected function getArrayableAppends(){
        if($this->wasRecentlyCreated){
            $this->appends = ['streamUsername','streamPassword'];
        }
        return parent::getArrayableAppends();
    }
    public function StreamById($user_id,$offset,$limit){
        $users=new Users();
        
        $user=DB::table('users')
            ->where("id",$user_id)
            ->first();
         unset($user->password);   

        $stream_data = DB::table('user_streams')
            ->where("user_id",$user_id)
            ->orderBy('created_at', 'DESC')
            ->offset($offset)
            ->limit($limit)		
            ->get()->toArray();
            
            foreach($stream_data as $stream){
                $status=$this->userStatus($stream->user_id,$user_id);
                $user->profile_picture_url=$users->getProfilePictureUrl($user->profile_picture);
                $stream->user_details=$user;
                $stream->user_details->is_friend=$status["is_friend"];
                $stream->user_details->is_follow=$status["is_follow"];
                
            }
        return $stream_data;  
        
    }
    public function StreamByName($user_id,$stream_name,$offset,$limit){
        $users=new Users();
        $stream_data = DB::table('user_streams')
            ->join("stream_user_actions","user_streams.user_id","stream_user_actions.user_id",'LEFT')
            ->where("stream_user_actions.type","!=","0")
            ->where('name','LIKE',"%".$stream_name."%")
            ->groupBy('user_streams.id')
            ->orderBy('user_streams.created_at', 'DESC')
            ->offset($offset)
            ->limit($limit)
            ->select("*")
            ->get();

            foreach($stream_data as $stream){
                $user_data = DB::table('users')
            ->where('id','=',$stream->user_id)
            ->select("id","role_id",'full_name',"email","status","profile_picture","is_picture","privacy_setting","is_verified","is_available")
            ->first();    
                
                
                 
                $status=$this->userStatus($user_id,$user_data->id);
                $user_data->is_friend=$status["is_friend"];
                $user_data->is_follow=$status["is_follow"];
                $user_data->profile_picture_url=$users->getProfilePictureUrl($user_data->profile_picture);                
                $stream->user_details=$user_data;
             }
             foreach($stream_data as $streams){
                if($streams->status=="0"){
                    //waiting
                    $streams->status_text="Waiting";
                }else if($streams->status=="10")
                {
                    // live
                    $streams->status_text="Live";
                }else{
                    // ended
                    $streams->status_text="Ended";
                }
               
            }
            

        return $stream_data;  
        
    }
    public function StreamByViewers($user_id){
        $forProfilePicture=new Users();
        $stream_data = DB::table('user_streams')
            ->leftjoin("stream_user_actions",'user_streams.id','stream_user_actions.stream_id')
            //->Where("stream_user_actions.type","=",0)
            ->whereNotIn('user_streams.id', function($q){
                 $q->select('user_streams.id')
                    ->leftjoin("stream_user_actions",'user_streams.id','stream_user_actions.stream_id')
                    ->select("user_streams.id")
                    ->from('user_streams')
                    ->where('stream_user_actions.type','=',0);
                // more where conditions
            })

            ->orderBy('total_viewers', 'DESC')
            ->groupBy("user_streams.id")
            ->offset(0)
            ->limit(10)
            ->select("user_streams.*")
           // ->select("stream_user_actions.*")
            ->get();

        /*  $stream_data = DB::table('user_streams')
            ->whereNotIn('user_streams.id', function($q){
                $q->select('type')
                    ->from('stream_user_actions')
                    ->where('stream_user_actions.type','!=',0);
                // more where conditions
            })
            ->orderBy('total_viewers', 'DESC')
            ->groupBy("user_streams.id")
            ->offset(0)
            ->limit(10)
            ->select("*")
            ->get();*/

            foreach($stream_data as $streams){
                if($streams->status=="0"){
                    //waiting
                    $streams->status_text="Waiting";
                }else if($streams->status=="10")
                {
                    // live
                    $streams->status_text="Live";
                }else{
                    // ended
                    $streams->status_text="Ended";
                }
              $status=$this->userStatus($user_id,$streams->user_id);  
                
            }
            
            foreach($stream_data as $stream){
                $user_data = DB::table('users')
            ->where('id','=',$stream->user_id)
            ->select("id","role_id",'username','full_name',"email","status","profile_picture","is_picture","privacy_setting","is_verified","is_available")
            ->first();  
                $user_data->profile_picture_url=$forProfilePicture->getProfilePictureUrl($user_data->profile_picture);
                $user_data->is_friend=$status["is_friend"];  
                $user_data->is_follow=$status["is_follow"];  
                $stream->user_details=$user_data;
            }

        return $stream_data;  
        
    }
    public function StreamByFilter($filter,$user_id,$offset,$limit){
        $forProfilePicture=new Users();
       $type="0";

        if($filter==0){

            // All Streams
            
            $stream_data = DB::table('user_streams')
                ->where("is_public","=","1")
                ->select("user_streams.*","user_streams.user_id as friend_user_id")
                ->orderBy('created_at', 'DESC')
                ->offset($offset)
                ->limit($limit)
                ->get();

               $tempForCount = DB::table('user_streams')
                ->select("user_streams.*","user_streams.user_id as friend_user_id")
                ->orderBy('id', 'desc')
                ->get();    
                
                
        }
        if($filter==1){
            // Newest Streams
            $stream_data = DB::table('user_streams')
                ->where("is_public","=","1")
                ->select("user_streams.*","user_streams.user_id as friend_user_id")
                ->orderBy('id', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();
           $tempForCount = DB::table('user_streams')
            ->select("user_streams.*","user_streams.user_id as friend_user_id")
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();    
                
                
        }
        if($filter==2){
            // from friends
            $type="10";
        }
        if($filter==3){
            // from followers
            $type="20";
        }
        
        if($type=='10'||$type=='20'){
            
            $stream_data = DB::table('friends')
                ->join('user_streams','friends.friend_user_id', '=', 'user_streams.user_id')
                ->join('users','friends.friend_user_id', '=', 'users.id')
                ->where("friends.user_id","=",$user_id)
                ->where("friends.type","=",$type)
                ->select("user_streams.*",'friends.friend_user_id')
                ->offset($offset)
                ->limit($limit)
                ->get();

             $tempForCount =DB::table('friends')
                ->join('user_streams','friends.friend_user_id', '=', 'user_streams.user_id')
                ->join('users','friends.friend_user_id', '=', 'users.id')
                ->where("friends.user_id","=",$user_id)
                ->where("friends.type","=",$type)
                ->select("user_streams.*",'friends.friend_user_id')
                 ->get();     
                
                
          }

        foreach($stream_data as $streams){
                if($streams->status=="0"){
                    //waiting
                    $streams->status_text="Waiting";
                }else if($streams->status=="10")
                {
                    // live
                    $streams->status_text="Live";
                }else{
                    // ended
                    $streams->status_text="Ended";
                }

              //   $streams->friend_status=$status;
                
            } 

             foreach($stream_data as $stream){
                $user_data=DB::table('users')
                ->where("id","=",$stream->friend_user_id)
                ->select("users.*")
                ->first();

                unset($user_data->password);
                $user_data->profile_picture_url=$forProfilePicture->getProfilePictureUrl($user_data->profile_picture);
                $stream->user_details=$user_data;
                $status=$this->userStatus($user_id,$stream->user_details->id);
               $stream->user_details->is_friend=$status["is_friend"];
                 $stream->user_details->is_follow=$status["is_follow"];
                        
             
             }    
             
        return array("stream_data"=>$stream_data,"total_count"=>count($tempForCount));
        
    }
    public function userStatus($user_id,$friend_user_id){
        
        $arr=array();
        $userfriendRequest=new UserFriendRequest();
        
        $friend= new Friend();
        
        $pending=$userfriendRequest::where("user_id",$user_id)->where("friend_user_id",$friend_user_id)->first();


         $friendFollow = DB::table('friends')
               ->where("user_id","=",$user_id)
                ->where("friend_user_id","=",$friend_user_id)
                ->select("friends.type")
                ->get();
        if(count($pending)==0 && count($friendFollow)==0){
            $arr["is_friend"]=30;
            $arr["is_follow"]=30;
            //return $arr;
        }else if(count($pending)!=0 && count($friendFollow)==0){
            $arr["is_follow"]=30;
            $arr["is_friend"]=$pending->status;
        }else if(count($pending)==0 && count($friendFollow)!=0){
            $arr["is_friend"]=30;
            $arr["is_follow"]=20;
            
        }
        else if(count($pending)!=0){
            $arr["is_friend"]=$pending->status;
        }
            foreach($friendFollow as $data){
                if($data->type==10){
                    $arr["is_friend"]=$data->type;
                    if(!isset($arr["is_follow"])){
                    $arr["is_follow"] = 30;
                    }
                    
                }
                if($data->type==20){
                    $arr["is_follow"]=$data->type;
                    if(!isset($arr["is_friend"])){
                    $arr["is_friend"] = 30;
                    }
                }
                
                    
         if(!isset($arr["is_follow"])){
            $arr["is_follow"] = 30;
            
         } 
         if(!isset($arr["is_friend"])){
            $arr["is_friend"] = 30;
            
         }      
            
            
        }

      return $arr;

    }
    
   

}
