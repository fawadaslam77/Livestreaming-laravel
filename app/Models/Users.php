<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use DB;

class Users extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    
    public static $STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
        
    ];

    
     protected $table = 'users';
    protected $fillable = ['role_id','full_name','username','status','status_text','dob'];
    protected $visible = ['id','role_id','full_name','username','status','status_text','dob','gender','city','state','country','address','phone','mobile','profile_picture','is_verified','last_login'
    ];
    
    public function UserModel($user_id){
        $userModel = DB::table('users')
            ->where("id",'=',$user_id)
            ->first();

         unset($userModel->password);
         
         $total_streams= DB::table('user_streams')
            ->where("user_id",'=',$user_id)
            ->count();
         $total_followers= DB::table('friends')
            ->where("user_id",'=',$user_id)
            ->where("type",'=',"20")
            ->count();
         $total_followings= DB::table('friends')
            ->where("friend_user_id",'=',$user_id)
            ->where("type","=","20")
            ->count();
         $total_friends= DB::table('friends')
            ->Where("user_id",'=',$user_id)
            ->where("type","=","10")
            ->count();
        $userModel->profile_picture_url=asset( 'public/'.Config::get('constants.front.dir.profilePicPath').$userModel->profile_picture);
        $userModel->total_streams=$total_streams;
        $userModel->total_followers=$total_followers;
        $userModel->total_followings=$total_followings; 
        $userModel->total_friends=$total_friends;  
         return $userModel;   
    }
    public function scopeBlockedUsers($query){
        
         return $query->where('status', STATUS_INACTIVE);
    }
    public function getProfilePictureUrl($profile_picture){
        return $profile_picture_url=asset( 'public/'.Config::get('constants.front.dir.profilePicPath').$profile_picture);
    }
    public static function isDeleted($username){
        $users = DB::table('users')->where('username', $username)->first();
        return $users;
    }
    public static function isEmailDeleted($email){
        $users = DB::table('users')->where('email', $email)->orWhere('username', $email)->first();
        return $users;
    }
    public static function reActiveEmail($email,$password){
        $user=DB::table('users')
            ->where('email', $email)
            ->update(['status' => "1",'password'=>$password ]);
        return $user;
    }
    public static function fromMobile($contactNo){
        $contactNo=str_replace(' ','' ,$contactNo);
        $user=DB::table('users')
            ->where('mobile_no','=', $contactNo)
            ->first();

        return $user;
    }

    
    
    

    
}
