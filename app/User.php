<?php

namespace App;

use DateTime;
use Config;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int role_id
 * @property string first_name
 * @property string last_name
 * @property string full_name
 * @property string username
 * @property string email
 * @property string password
 * @property int status
 * @property string status_text
 * @property string dob
 * @property string gender
 * @property string address
 * @property string city
 * @property string state
 * @property string country
 * @property string postal_code
 * @property string phone
 * @property string mobile_no
 * @property string profile_picture
 * @property int privacy_setting
 * @property int notification_status
 * @property string device_type
 * @property string device_token
 * @property string verification_code
 * @property int is_verified
 * @property int is_subadmin
 * @property int is_available
 * @property int remember_token
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 * @property string last_login
 *
 * @property \App\Models\Role $role
 * @property \App\Models\SocialAccount[] $socialAccounts
 * @property \App\Models\ContactForm[] $contactForms
 * @property \App\Models\UserStream[] $streams
 * @property \App\Models\UserPackage[] $userPackages
 * @property \App\Models\StreamUserTag[] $streamTags
 * @property \App\Models\StreamUserAction[] $streamActions
 * @property \App\Models\Friend[] $friends
 * @property \App\Models\UserFriendRequest[] $friendRequestsSent
 * @property \App\Models\UserFriendRequest[] $friendRequests
 */
class User extends Authenticatable
{
    //use Notifiable;
    use SoftDeletes;

    public $token;

    const STATUS_ACTIVE             = 1;
    const STATUS_BLOCKED            = 0;

    const NOTIFY_ACTIVE             = 1;
    const NOTIFY_INACTIVE           = 0;

    public static $STATUS_TEXT = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_BLOCKED => 'Blocked',
    ];

    const TYPE_EMAIL                = 'email';
    const TYPE_FACEBOOK             = 'fb';
    const TYPE_TWITTER              = 'tw';

    const DEVICE_TYPE_ANDROID       = 'android';
    const DEVICE_TYPE_IOS           = 'ios' ;
    const DEVICE_TYPE_WEB           = 'web';

    const SOCIALMEDIA_PLATFORM_FB   = 'fb';
    const SOCIALMEDIA_PLATFORM_TW   = 'tw';

    const PRIVACY_NOONE             = 0;
    const PRIVACY_FRIENDS_ONLY      = 10;
    const PRIVACY_FOLLOWERS_ONLY    = 20;
    const PRIVACY_FRIENDS_FOLLOWERS = 30;
    const PRIVACY_EVERYONE          = 40;

    public static $PRIVACY_TEXT = [
        self::PRIVACY_NOONE            => 'No One',
        self::PRIVACY_FRIENDS_ONLY     => 'Friends Only',
        self::PRIVACY_FOLLOWERS_ONLY   => 'Followers Only',
        self::PRIVACY_FRIENDS_FOLLOWERS=> 'Friends and Followers',
        self::PRIVACY_EVERYONE         => 'Everyone'
    ];

    const GENDER_MALE               = 'male';
    const GENDER_FEMALE             = 'female';

    public static $GENDER_TEXT = [
        self::GENDER_MALE            => 'Male',
        self::GENDER_FEMALE          => 'Female',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'first_name', 'last_name','full_name', 'username', 'email', 'password','gender', 'address', 'city', 'state', 'country', 'postal_code', 'phone', 'mobile_no','device_type','device_token', 'profile_picture','is_picture','is_purchased','status','status_text','notification_status','privacy_setting', 'verification_code','is_verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be visible in arrays
     *
     * @var array
     */
    protected $visible = [
        'id','role_id', 'full_name','username', 'email', 'password','gender','mobile_no','device_type','device_token','is_purchased','status','status_text_value','status_text', 'role_name', 'profile_picture_url','is_picture','notification_status','privacy_setting','verification_code','is_verified'
    ]; // 'first_name', 'last_name',  'address', 'city', 'state', 'country', 'postal_code', 'phone', 'profile_picture'

    /**
     * The extra attributes that should be appended in arrays
     *
     * @var array
     */
    protected $appends = ['status_text_value', 'role_name','profile_picture_url']; //, 'role_details'

    /**
     * Set the default values of attributes
     *
     * @var array
     */
    protected $attributes = ['status'=>self::STATUS_ACTIVE,'notification_status'=>self::NOTIFY_ACTIVE, 'privacy_setting'=>self::PRIVACY_EVERYONE, 'is_verified'=>0, 'is_picture'=>0];

    // user has a role
    public function role()
    {
        return $this->belongsTo('App\Models\Role','role_id');
    }


    // user has many social accounts
    public function socialAccounts()
    {
        return $this->hasMany('App\Models\SocialAccount','user_id');
    }

    // user has many contact forms
    public function contactForms()
    {
        return $this->hasMany('App\Models\ContactForm','user_id');
    }

    // user has many streams
    public function streams()
    {
        return $this->hasMany('App\Models\UserStream','user_id');
    }

    // user has many user packages
    public function userPackages()
    {
        return $this->hasMany('App\Models\UserPackage','user_id');
    }

    // user has many stream tags
    public function streamTags()
    {
        return $this->hasMany('App\Models\StreamUserTag','user_id');
    }

    // user has many stream actions
    public function streamActions()
    {
        return $this->hasMany('App\Models\StreamUserAction','user_id');
    }

    // user has many friends
    public function friends()
    {
        return $this->hasMany('App\Models\Friend','user_id');
    }

    // user has many friend request sent
    public function friendRequestsSent()
    {
        return $this->hasMany('App\Models\UserFriendRequest','user_id');
    }

    // user has many friend request received
    public function friendRequests()
    {
        return $this->hasMany('App\Models\UserFriendRequest','friend_user_id');
    }

    // user has many permissions
    public function permissions()
    {
        return $this->role->nestedPermissions;
    }

    public function checkPermission($name){
        return $this->role->checkPermissionByName($name);
    }

    public function isAdmin(){
        return $this->role->is_admin;
    }


    public function getStatusTextValueAttribute(){
        return self::$STATUS_TEXT[$this->status];
    }

    public function getRoleDetailsAttribute(){
        return $this->role;
    }

    public function getRoleNameAttribute(){
        return $this->role->name;
    }

    public function getProfilePictureUrlAttribute(){
        return asset( 'public/'.Config::get('constants.front.dir.profilePicPath') . ($this->profile_picture ?: Config::get('constants.front.default.profilePic')) );
    }

    /**
     * Scope a query to only include users that have Admin roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdminsOnly($query)
    {
        return $query->with('role')->whereHas('role', function($query) {
            $query->where('is_admin',1);
        });
    }

    /**
     * Scope a query to only include users that have Admin roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsersOnly($query)
    {
        return $query->with('role')->whereHas('role', function($query) {
            $query->where('is_admin',0);
        });
    }

    /**
     * Scope a query to only include users that have ios in device type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIosOnly($query)
    {
        return $query->where('device_type',self::DEVICE_TYPE_IOS);
    }

    /**
     * Scope a query to only include users that have android in device type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAndroidOnly($query)
    {
        return $query->where('device_type',self::DEVICE_TYPE_ANDROID);
    }

    /**
     * Scope a query to only include users who registered after a specific time.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  \DateTime $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRegisteredAfter($query, DateTime $date)
    {
        return $query->where('created_at', '>=',$date->format('Y-m-d H:i:s'));
    }

    /**
     * Scope a query to return count group by registration month after a specific time.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  \DateTime $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupByRegistrationMonth($query,DateTime $after)
    {
        //return $query->where('created_at', '>=',$date->format('Y-m-d H:i:s'));
        return $query->usersOnly()->having('monthyear', '>=', $after->format('Y-m'))->groupBy('monthyear')->selectRaw("count(*) as users, DATE_FORMAT(`created_at`,'%Y-%m') as monthyear")->pluck('users','monthyear');

    }

    /**
     * Scope a query to return count group by registration month with type ios after a specific time.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  \DateTime $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupByIosRegistrationMonth($query,DateTime $after)
    {
        return $query->iosOnly()->groupByRegistrationMonth($after);
    }

    /**
     * Scope a query to return count group by registration month with type android after a specific time.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  \DateTime $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupByAndroidRegistrationMonth($query, DateTime $after)
    {
        return $query->androidOnly()->groupByRegistrationMonth($after);

    }

}
