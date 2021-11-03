<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Notification;
use DB;

/**
 * This is the model class for table "friends".
 *
 * @property int $id
 * @property int $user_id
 * @property int $friend_user_id
 * @property int $type
 * @property int $notification_status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property \App\User $user
 * @property \App\User $friend
 * @property \App\Models\FriendMedium[] $mediums
 */
class Friend extends Model
{
    use SoftDeletes;

    const TYPE_BLOCK = 0;
    const TYPE_FRIEND = 10;
    const TYPE_FOLLOW = 20;

    public static $TYPES = [
        self::TYPE_BLOCK => 'Block',
        self::TYPE_FOLLOW => 'Follow',
        self::TYPE_FRIEND => 'Friend'
    ];

    protected $fillable = ['user_id','friend_id','type', 'notification_status'];
    protected $visible = ['user_id','friend_id','type', 'notification_status','created_at','updated_at',
        'type_text'
    ];
    protected $attributes = ['type'=>self::TYPE_FOLLOW, 'notification_status'=>1];
    protected $appends = ['type_text'];

    // friends has a user
    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    // friends has a friend user
    public function friend()
    {
        return $this->belongsTo('App\User','friend_user_id');
    }

    public function mediums(){
        return $this->hasMany('App\Models\FriendMedium', 'friends_id');
    }

    public function getTypeTextAttribute(){
        return self::$TYPES[$this->type];
    }
    public function user_friend_list($user_id,$action,$offset,$limit){
            $userStream=new UserStream();
            $forProfilePic=new Users();
            $users=DB::table('friends')
                    ->where("user_id",'=',$user_id)
                    ->where("friends.type",'=',$action)
                    ->select("friends.*")
                    ->offset($offset)
                    ->limit($limit)
                    ->get();
          
            
            foreach($users as $user){
              $user->user_details=DB::table('users')
                    ->where("id",'=',$user->friend_user_id)
                    ->select("users.id",'role_id','full_name','username','email','status','status_text','gender','is_picture','profile_picture','privacy_setting')
                    ->first();

                
                
                $friendStatus=$userStream->userStatus($user_id,$user->id);
               
               
                $user->user_details->is_friend=10;//$friendStatus["is_friend"];
                $user->user_details->is_follow=$friendStatus["is_follow"];
                
                 $total_streams= DB::table('user_streams')
                    ->where("user_id",'=',$user->id)
                    ->count();
                 $total_followers= DB::table('friends')
                    ->where("user_id",'=',$user->id)
                    ->count();
                 $total_followings= DB::table('friends')
                    ->where("friend_user_id",'=',$user->id)
                    ->count();
                 $total_friends= DB::table('user_friend_requests')
                    ->where("user_id",'=',$user->id)
                    ->count();
                
                $user->user_details->total_streams=$total_streams;
                $user->user_details->total_followers=$total_followers;
                $user->user_details->total_followings=$total_followings;
                $user->user_details->$total_friends=$total_friends;
                $user->user_details->profile_picture_url=$forProfilePic->getProfilePictureUrl($user->user_details->profile_picture);
                
                
                
                
                
                
                
                
                
                
                unset($user->password);
                //if($user->user_details->status=="1"){
//                    $user->user_details->status_text="Active";
//                }else{
//                    $user->user_details->status_text="Inactive / Blocked";
//                }
            }
            
     
            
         return $users;
        
    }
    public function blocked_list_user($user_id,$action,$offset,$limit){ //temporary
            //$users = new \stdClass();
            $forProfilePic=new Users();
            $users=DB::table('friends')
                    ->where("user_id",'=',$user_id)
                    ->where("type",'=',"0")
                    ->select("friends.*")
                    ->offset($offset)
                    ->limit($limit)
                    ->get();
          
          if(count($users)!=0){  
            foreach($users as $user){
              $user->user_details=DB::table('users')
                    ->where("id",'=',$user->friend_user_id)
                    ->select("users.*")
                    ->first();
             unset($user->user_details->password);
            
           // return $user->user_details["profile_picture"];
               if(!isset($user->user_details->profile_picture)){
                   $user->user_details->profile_picture_url= asset( 'public/'.Config::get('constants.front.dir.profilePicPath')."default.jpg");
               }else{
                    $user->user_details->profile_picture_url=$forProfilePic->getProfilePictureUrl($user->user_details->profile_picture);
                }
            }
           }else{
                $user="";
           }
            
     
            
         return $users;
        
    }
    public function user_following_list($user_id,$action,$offset,$limit){ // jinha user na follow kea hua 
        if($offset=="0" && $limit=="0"){
            $offset="0";
            $limit="1000000";
        }
        
        $users=DB::table('friends')
                    ->where("friend_user_id",'=',$user_id)
                    ->where("friends.type",'=',$action)
                    ->select("friends.*")
                    ->offset($offset)
                    ->limit($limit)
                    ->get();
          
            
            foreach($users as $user){
              $user->user_details=DB::table('users')
                    ->where("id",'=',$user->user_id)
                    ->select("users.id",'role_id','full_name','username','email','status','status_text','gender','is_picture','profile_picture','privacy_setting')
                    ->first();
                 
                
                
                unset($user->password);
                if($user->user_details->status=="1"){
                    $user->user_details->status_text="Active";
                }else{
                    $user->user_details->status_text="Inactive / Blocked";
                }
            }
            
            
     
            
         return $users;
    }
     public function user_followed_list($user_id,$action,$offset,$limit){
        if($offset=="0" && $limit=="0"){
            $offset="0";
            $limit="1000000";
        }
        $users=DB::table('friends')
                    ->where("user_id",'=',$user_id)
                    ->where("friends.type",'=',$action)
                    ->select("friends.*")
                    ->offset($offset)
                    ->limit($limit)
                    ->get();
          
            
            foreach($users as $user){
              $user->user_details=DB::table('users')
                    ->where("id",'=',$user->friend_user_id)
                    ->select("users.id",'role_id','full_name','username','email','status','status_text','gender','is_picture','profile_picture','privacy_setting')
                    ->first();
                 
                
                
                unset($user->password);
                if($user->user_details->status=="1"){
                    $user->user_details->status_text="Active";
                }else{
                    $user->user_details->status_text="Inactive / Blocked";
                }
            }
            
     
            
         return $users;
    }
    public function search_friend($user_id,$search_string){
        $friendStatus="";
        $userStream=new UserStream();
        $forProfilePic=new Users();
        
        $users = DB::table('users')
			->whereNotIn("id",[$user_id])
            ->where("role_id","!=","3")
            ->where("status","!=","0")
            ->where("id","!=",$user_id)
            ->where('username',"LIKE",$search_string.'%')
            ->orWhere("full_name",'LIKE',$search_string.'%')
            ->groupBy("username")
            ->select('id','role_id','full_name','username','email','gender','profile_picture','is_picture','privacy_setting','is_verified','status','status_text')
            ->get();
           
         if(count($users)!=0){   
            foreach($users as $user){
                //if($user->id==$user_id){
//                    continue;
//                }
                unset($user->password);
                $friendStatus=$userStream->userStatus($user_id,$user->id);
               
               
                $user->is_friend=$friendStatus["is_friend"];
                $user->is_follow=$friendStatus["is_follow"];
                
                 $total_streams= DB::table('user_streams')
                    ->where("user_id",'=',$user->id)
                    ->count();
                 $total_followers= DB::table('friends')
                    ->where("user_id",'=',$user->id)
                    ->count();
                 $total_followings= DB::table('friends')
                    ->where("friend_user_id",'=',$user->id)
                    ->count();
                 $total_friends= DB::table('user_friend_requests')
                    ->where("user_id",'=',$user->id)
                    ->count();
                $is_block= DB::table('friends')
                    ->where("friend_user_id",'=',$user->id)
                    ->where("user_id",'=',$user_id)
                    ->where("type",'=',0)
                    ->count();
                if($is_block > 0){
                    $user->is_block=1;
                }
                else{
                    $user->is_block=0;
                }
                $user->total_streams=$total_streams;
                $user->total_followers=$total_followers;
                $user->total_followings=$total_followings;
                $user->$total_friends=$total_friends;
                $user->profile_picture_url=$forProfilePic->getProfilePictureUrl($user->profile_picture);
                
                
                //$users=$this->total_counts($users,$user->id);
            }
         }else{
            $users=[];
         }   
           
         return $users;
        
    }
    public function friend_action($user_id,$friend_user_id,$action){
       // $data=array("user_id"=>$user_id,"friend_user_id"=>$friend_user_id,"type"=>$action,"notification_status"=>"0","created_at"=>date("Y-m-d H:i:s"),"updated_at"=>date("Y-m-d H:i:s"));
        //$insertCheck=DB::table("friends")->insert($data);
       $insertCheck=DB::insert('insert into friends (user_id, friend_user_id,type,notification_status) values (?, ?,?,?)', [$user_id,$friend_user_id,$action,"0"]);  
       return $friend_user_id;
                
    }
    
    public function update_friend_action($primary_id,$action){
        $data=DB::table('friends')
            ->where('id',$primary_id )
            ->update(['type' => $action]);
       
        return $primary_id; 
        
    }
    public function update_friend_by_both($user_id,$friend_id,$action){
        $data=DB::table('friends')
            ->where('user_id',$user_id )
            ->where('friend_user_id',$friend_id )
            ->update(['type' => $action]);
       return $friend_id;
      
        
    }
    
    public function get_friend_by_id($primary_id,$action){
         $data=DB::table('friends')
            ->where('id',$primary_id )
            ->where('type',$action)
            ->select("friends.*")
            ->first();
       
        return $data;
        
    }
    
    public function get_friendTableData($user_id,$follow_user){
        $friendTableData = DB::table('friends')
			->where('user_id','=',$user_id) 
            ->where("friend_user_id","=",$follow_user)
            ->select('friends.*')
            ->get();
        
        return $friendTableData;    
    }
//    public function follow_user($user_id,$friend_user_id){
//        $data=array("user_id"=>$user_id,"friend_user_id"=>$friend_user_id,"type"=>"20","notification_status"=>"0","created_at"=>date("Y-m-d H:i:s"),"updated_at"=>date("Y-m-d H:i:s"));
//        DB::table("friends")->insert($data);
//        return $data;      
//    }
    public function check_follower_byType($user_id,$follow_user,$type){
         $is_followed = DB::table('friends')
			
            ->where('friends.user_id','=',$user_id) 
            ->where("friends.friend_user_id","=",$follow_user)
            ->where("friends.type","=",$type)
        	->select('friends.*')
            ->first();
         return $is_followed;
        
    }
    public function unfriend($user_id,$friend_user_id){
        DB::table('friends')
			->where('user_id','=',$user_id) 
            ->where("friend_user_id","=",$friend_user_id)
            ->where("type","=","10")
            ->orwhere("type","=","0")
        	->delete();
        DB::table('friends')
			->where('friend_user_id','=',$user_id) 
            ->where("user_id","=",$friend_user_id)
            ->where("type","=","10")
            ->orwhere("type","=","0")
        	->delete();
        DB::table('user_friend_requests')
			->where('user_id','=',$user_id) 
            ->where("friend_user_id","=",$friend_user_id)
            ->where("status","=","10")
            ->orwhere("status","=","0")
        	->delete();
        return "Record Deleted Successfully";
    }
    //public function getFriendsDevices($user_id,$title,$body){
//        $notification=new Notification();
//        
//        $users=DB::table('friends')
//                    ->where("user_id",'=',$user_id)
//                    ->where("type",'=',"10")
//                    ->select("friends.*")
//                    ->get();
//          
//            
//            foreach($users as $user){
//              $user->device_details=DB::table('user_devices')
//                    ->where("user_id",'=',$user->friend_user_id)
//                    ->select("user_devices.*")
//                    ->first();
//             $notification->send_notification($user->ud_id,$title,$body);      
//             
//            }
//            
//        return $users;
//        
//    }    
    
    
}
