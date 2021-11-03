<?php

namespace App\Http\Controllers\Api;

use App\User;
//use App\Friend;
use Hash;
use Config;
use JWTAuth;
use Validator;
use App\Http\Traits\JWTUserTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\UpdateDeviceRequest;
use App\Http\Requests\Api\UpdateNotificationRequest;
use App\Http\Requests\Api\UserExistVerificationRequest;
use DB;
use App\Models\Users;
use App\Models\UserStream;
use App\Models\Notification;
use App\Models\Friend;
use Input;




class ProfileController extends BaseController
{
    
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getUserDetails(Request $request){
        $user = JWTUserTrait::getUserInstance($request);
        if($user instanceof \App\User) {
            return Controller::returnResponse(200, 'User Details', ['user' => $user->toArray()]);
        }
        return Controller::returnResponse(403,'Invalid Token or User Id', []);
    }

    public function updateNotificationStatus(UpdateNotificationRequest $request) {

        $input             = $request->all();
        $userId            = $request->input('user_id', null);
        $status            = $request->input('status', null);

        $user = User::find($userId);
        $user->notification_status = ($status == 0) ? 0 : 1;
        $user->save();

        return Controller::returnResponse(200, 'Notification status updated successfully', ['user'=>$user]);

    }

    public function updateDeviceToken(UpdateDeviceRequest $request) {

        $input             = $request->all();
        $userId            = $input['user_id'];

        $user = User::find($userId);
        $dataToUpdate = array_filter([
            'device_type' => $request->get('device_type', null),
            'device_token' => $request->get('device_token', null)
        ]);
        $user->update($dataToUpdate);

        return Controller::returnResponse(200, 'Device token updated successfully', ['user'=>$user]);

    }

    /**
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request) {

        $userId = $request->input('user_id', null);
        $user = User::find($userId);
        $old_pass=$request->input('old_password'); 
        $status=(string)Hash::check($old_pass, $user->password);
         if($status!=1){
            return Controller::returnResponse(403,'Invalid Old Password', []);
         }
        if (!$user) {
            return Controller::returnResponse(403,'Something went wrong here.', []);
        }

        $dataToUpdate = [];

        if ($request->has('password') && $request->input('password', '') !== '') {
            $dataToUpdate['password'] = \Hash::make($request->input('password'));
        }

        if (empty($dataToUpdate)) {
            return Controller::returnResponse(400,'Nothing to update', []);
        }

        $user->update($dataToUpdate);

        return Controller::returnResponse(200, 'Password changed successfully', []);

    }

    public function updateUser(UpdateProfileRequest $request) {

        $input             = $request->all();
        $userId            = $input['user_id'];
        $oldPassword       = isset($input['old_password']) ? $input['old_password'] : "";

        $user              = User::find($userId);

        if($user) {
            $dataToUpdate = array_filter([
                'full_name' => $request->get('full_name', null),
                'gender' => $request->get('gender', null),
                'status_text' => $request->get('status_text', null),
               // 'mobile_no' => $request->get('mobile_no', null)
            ]);
            if($request->route()->getName()=="withUsername"){
                // This call is between registration process. Set the username;
                $dataToUpdate['username'] = $request->get('username',null);
            }
            if ($request->has('password') && $request->get('password', '') !== '') {
                // UPDATE: Old password will be checked by the Request Class
                // checking old password is correct ....
                /*if ($request->has('old_password') && $request->get('old_password', '') !== '') {
                    $loginattemp['email']   =  $user->email;
                    $loginattemp['password'] =  $oldPassword;
                    // checking old Password ....
                    if (!$token = JWTAuth::attempt($loginattemp)) {
                        return Controller::returnResponse(403,'Wrong old password provided', []);
                    }
                    $dataToUpdate['password'] = \Hash::make($request->get('password'));
                }*/
                $dataToUpdate['password'] = \Hash::make($request->get('password'));
            }

            if ($request->hasFile('profile_picture')) {
                $imageName = $user->id . '-' . \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
                $path = public_path(Config::get('constants.front.dir.profilePicPath'));
                $request->file('profile_picture')->move($path, $imageName);
                $dataToUpdate['profile_picture'] = $imageName;
            }

            if (empty($dataToUpdate)) {
                return Controller::returnResponse(400, 'Nothing to update', []);
            }

            $user->update($dataToUpdate);
            if($request->route()->getName()=="withUsername") {
                $token = JWTUserTrait::extractToken();
                $user = $user->toArray();
                $user['token'] = $token;
            }

            $message = "Your changes are updated. Thank you!";
            return Controller::returnResponse(200, $message, ['user'=>$user]);

        } else {
            // if no user object found .....
            return Controller::returnResponse(400, 'No user found for the given id', []);
        }
    }
    public function user_details(Request $request)
    {
        $currentUser = JWTUserTrait::getUserInstance();
        $user=new Users();
        $forProfilePic=new User();
        $userStream=new UserStream();
        $result=$user->UserModel($request->user_id);
        $friend_status=$userStream->userStatus($currentUser->id,$result->id);
        if($result->profile_picture==null || $result->profile_picture==" " ){
        $result->profile_picture_url=$forProfilePic->getProfilePictureUrlAttribute();
        }        
        $result->is_friend=$friend_status["is_friend"];
        $result->is_follow=$friend_status["is_follow"];
        //$result=$user->find($request->user_id);
            if(count($result)==0){
                return Controller::returnResponse(400, 'No Record Found', []);
            }
            if($result->role_id=="3"){
                return Controller::returnResponse(400, 'You Are Not Authorized To Search Admin', []);
            }
        $message="User Data Found";
        return Controller::returnResponse(200, $message, ['user'=>$result]);
    }
    
    public function delete_user(UserExistVerificationRequest $request){
        
        $id=$request->input("user_id");
        $user =new Users(); //JWTUserTrait::getUserInstance();
        $result=$user->find($id);
        DB::table('users')
            ->where('id', $result->id)
            ->update(['status' => 0]);
        $message="User Account Delete Successfully";
        return Controller::returnResponse(200, $message, ["user_details"=>$result]);

    }
    public function privacy_setting(){
        
        $user = JWTUserTrait::getUserInstance();
        
        $userObj=new Users();
        
        $user_data=$userObj->find($user->id);
        $message="User Privacy Setting Found";
        return Controller::returnResponse(200, $message, ["id"=>$user_data->id,"username"=>$user_data->username,"email"=>$user_data->email,"privacy_setting"=>$user_data->privacy_setting]);
    } 
    
    
    // services for notification
    public function getUserDevices(){
        $user_id=Input::get("user_id");
        $registration_id=Input::get("registration_id");
        $device_name=(Int)Input::get("device_name");
        if($device_name==1){
            $ud_id=Input::get("ud_id");
        }else{
            $ud_id=Input::get("registration_id");  
        }
        
        $notification=new Notification();
        $return=$notification->getUserDevices($user_id,$ud_id,$registration_id);
        
        if(count($return)==0){
           $message="User Device Data Inserted";
           $return=$notification->setUserDevices($user_id,$ud_id,$registration_id);
        }else{
          $message="User Device Data Updated";
          $return=$notification->updateUserDevice($user_id,$ud_id,$registration_id);
        }
        return Controller::returnResponse(200, $message, []);
        
  
    }
    

    

}