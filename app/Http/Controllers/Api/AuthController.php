<?php

namespace App\Http\Controllers\Api;


use App\Http\Requests\Api\AuthForgotPasswordPhoneRequest;

//use Laravel\Socialite\Facades\Socialite
use App\Models\SocialAccount;
use App\Http\Requests\Api\AuthSendVerificationCodeRequest;
use App\Http\Requests\Api\AuthVerifyCodeRequest;
use App\Http\Requests\Api\AuthVerifyForgotPasswordCodeRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Traits\JWTUserTrait;
use App\Models\Users;
use Hash;
use Config;
use JWTAuth;
use Validator;
use App\Http\Requests\Api\AuthRegisterRequest;
use App\Http\Requests\Api\AuthLoginRequest;
use App\Http\Requests\Api\AuthRenewTokenRequest;
use App\Http\Requests\Api\AuthForgotPasswordRequest;
use App\Http\Requests\Api\AuthUsernameAvailabilityRequest;

use App\Models\Setting;
use App\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use DB;
use Mail;
use Socialite;
//use Laravel\Socialite\Facades\Socialite;


class AuthController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function guestUserToken(Request $request)
    {
        $projectname = $request->input('project_name');
        $token = base64_encode($projectname);
        $result = [];
        $result['token'] = $token;
        return Controller::returnResponse(200, 'Guest User Token', $result);
    }

    public function register(AuthRegisterRequest $request)
    {
        $input = $request->all();

        $input['password'] = Hash::make($input['password']);
        $input['role_id'] = Setting::extract('app.default.user_role', 2);

        if ($request->hasFile('profile_picture')) {
            $imageName = '-' . \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $path = public_path(Config::get('constants.front.dir.profilePicPath'));
            $request->file('profile_picture')->move($path, $imageName);
            $input['profile_picture'] = $imageName;
        }
        //$is_deleted=Users::isEmailDeleted($request->input("email"));


       /* if(count($is_deleted)!=0){

            if($is_deleted->status==0){

                $status=Users::reActiveEmail($request->input("email"),$input["password"]);

            }else{

                $errors=array("email"=>["email already found in our system, please try another one"]);
                $message = "email already found in our system, please try another one.";
                $message = $errors['email'][0];


                return Controller::returnResponse(400, $message,['errors'=>$errors]);
            }

        }else {*/
            $userCreated = User::create($input);
        //}


        $loginData = $request->only(['email','password']);
        $loginData['role_id'] = $input['role_id'];
        return $this->doLogin($loginData, true);
    }

    public function isEmail(Request $request)
    {
        $input = $request->all();
        $is_deleted=Users::isEmailDeleted($request->input("email"));


        if(count($is_deleted)!=0){

            if($is_deleted->status==0){
                return Controller::returnResponse(400, 'Email alreday Exist',[]);
                $status=Users::reActiveEmail($request->input("email"),$input["password"]);

            }else{
                return Controller::returnResponse(400, 'Your account is deleted please contact to admin',[]);
            }

        }else {
            return Controller::returnResponse(200, 'This is new email',[]);
        }
    }

    public function fbLogin(Request $request)
    {
        $user_id = '';
        try {
            $user = Socialite::driver('facebook')->userFromToken($request->input("access_token"));
            //$user = Socialite::driver('facebook')->userFromToken('EAAe5Wceh8L0BAAQqvc6LZChiWBfozSlzt6p3JcKgid549rCFkBinpNGG6iNjl2mZAb4vv0Uy5m4kVVpNApXfJGxFEyA9cDV5cAW182f2UonCstrBfBZAwWSFXZCyKRtiJte7YZBgYkAmEONbZBrTsjvM5EjFeZBh0xl6zjRQw6n2hC2HFKHp0rBo2ZAvXMbHKJmbbtusXKnC96ZCbhUZBukeoR1X0lg037vESAr0eSYveKeyGrFJTVJVgw');
          /*print_r($user->avatar_original);
          echo $user->avatar_original;
            die();*/
            $socialUser = null;

            //Check is this email present

            //die();
            if (!isset($user['email'])) {
                $email = 'missing' . str_random(10);
            }
            else{
                $userCheck = User::where('email', '=', $user['email'])->first();
                $email = $user['email'];

            }
            if (!empty($userCheck)) {
                $socialUser = $userCheck;
            }
            else {
                $sameSocialId = DB::table('social_accounts')
                    ->where('client_id', '=', $user['id'])
                    ->where('platform', '=', 'facebook')
                    ->first();
                if (empty($sameSocialId)) {
                    //There is no combination of this social id and provider, so create new one
                    $newSocialUser = new User;
                    $newSocialUser->email              = $email;
                    $name = explode(' ', $user['name']);
                    if (count($name) >= 1) {
                        $newSocialUser->first_name = $name[0];
                    }
                    if (count($name) >= 2) {
                        $newSocialUser->last_name = $name[1];
                    }
                    $newSocialUser->full_name =  $user['name'];
                    $newSocialUser->password = bcrypt(str_random(16));
                    $newSocialUser->token = str_random(64);
                    //$newSocialUser->activated = true; //!config('settings.activation');
                    $newSocialUser->status = 1; //!config('settings.activation');
                    $newSocialUser->role_id = 2;
                    $newSocialUser->profile_picture = $user->avatar_original;
                    $newSocialUser->save();
                    $socialData = new SocialAccount;
                    $socialData->client_id = $user['id'];
                    $socialData->user_id = $newSocialUser->id;
                    $user_id = $newSocialUser->id;
                    $socialData->platform= 'facebook';
                    $socialData->token = str_random(64);
                    $socialData->save();
                    $socialUser = $newSocialUser;
                }
                else {
                    $socialUser = $sameSocialId;

                }

            }
            $user = DB::table('users')
                ->where('id', '=', $socialUser->user_id)
                ->first();

            if (!$token = JWTAuth::fromUser($user)) {
                return Controller::returnResponse(403,'Invalid credentials, please try log in again', []);
            }
            //die($token);
            $user->token = $token;
            //array("User"=>$user);


            return Controller::returnResponse(200, "This is a valid username", array("User"=>$user));

            //auth()->login($socialUser, true);
            // end verify user

        } catch (\Exception $e){echo   $e->getMessage();
           $arr = explode('response:', $e->getMessage());
           // return $arr[1];
         // echo   $array = json_decode(json_encode($e->getMessage()));
           // echo $array->['Client error'];
           // print_r(json_decode(json_encode($e->getMessage())));
        }
    }
    public function twtLogin(Request $request)
    {
        //echo $request->input('access_token');
        try {
           $user = Socialite::driver('twitter')->userFromTokenAndSecret($request->input('access_token'),$request->input('access_secret'));
           //$user = Socialite::driver('twitter')->userFromTokenAndSecret('284627359-x0wemSunpK341btqF2NnZIgwYiR8ItY4n8uRIBPB','2VInG0JDRflAXhnlnmgA4GQ0lpIHs6LcSZkZRkHc9kXrS');
            $socialUser = null;
            if (!isset($user->email)) {
                $email = 'missing' . str_random(10);
            }
            else{
                $userCheck = User::where('email', '=', $user->email)->first();
                $email = $user->email;
            }
            if (!empty($userCheck)) {
                $socialUser = $userCheck;
            }
            else {
                $sameSocialId = DB::table('social_accounts')
                    ->where('client_id', '=', $user->id)
                    ->where('platform', '=', 'twitter')
                    ->first();
                if (empty($sameSocialId)) {
                    //There is no combination of this social id and provider, so create new one
                    $newSocialUser = new User;
                    $newSocialUser->email              = $email;
                    $name = explode(' ', $user->name);
                    if (count($name) >= 1) {
                        $newSocialUser->first_name = $name[0];
                    }
                    if (count($name) >= 2) {
                        $newSocialUser->last_name = $name[1];
                    }
                    $newSocialUser->full_name =  $user->name;
                    $newSocialUser->password = bcrypt(str_random(16));
                    $newSocialUser->token = str_random(64);
                    //$newSocialUser->activated = true; //!config('settings.activation');
                    $newSocialUser->status = 1; //!config('settings.activation');
                    $newSocialUser->role_id = 2;
                    $newSocialUser->profile_picture = $user->avatar;
                    $newSocialUser->save();
                    $socialData = new SocialAccount;
                    $socialData->client_id = $user->id;
                    $socialData->user_id = $newSocialUser->id;
                    $user_id = $newSocialUser->id;
                    $socialData->platform= 'twitter';
                    $socialData->token = str_random(64);
                    $socialData->save();
                    $socialUser = $newSocialUser;
                }
                else {
                    $socialUser = $sameSocialId;

                }

            }
            $user = DB::table('users')
                ->where('id', '=', $socialUser->user_id)
                ->first();
           // print_r($user);
            if (!$token = JWTAuth::fromUser($user)) {
                return Controller::returnResponse(403,'Invalid credentials, please try log in again', []);
            }
            //die($token);
            $user->token = $token;
            //array("User"=>$user);


            return Controller::returnResponse(200, "This is a valid username", array("User"=>$user));
            //end validation


        } catch (\Exception $e){
            return $e;
           // echo   $e->getMessage();
           //$arr = explode('response:', $e->getMessage());
           // return $arr[1];
         // echo   $array = json_decode(json_encode($e->getMessage()));
           // echo $array->['Client error'];
           // print_r(json_decode(json_encode($e->getMessage())));
        }
    }
    public function googleLogin(Request $request)
    {
        //echo $request->input('access_token');
        //die();
        try {
            $user = Socialite::driver('google')->userFromToken($request->input('access_token'));
            //$user = Socialite::driver('twitter')->userFromTokenAndSecret('284627359-x0wemSunpK341btqF2NnZIgwYiR8ItY4n8uRIBPB','2VInG0JDRflAXhnlnmgA4GQ0lpIHs6LcSZkZRkHc9kXrS');
            $socialUser = null;
            if (!isset($user->email)) {
                $email = 'missing' . str_random(10);
            }
            else{
                $userCheck = User::where('email', '=', $user->email)->first();
                $email = $user->email;
            }
            if (!empty($userCheck)) {
                $socialUser = $userCheck;
            }
            else {
                $sameSocialId = DB::table('social_accounts')
                    ->where('client_id', '=', $user->id)
                    ->where('platform', '=', 'twitter')
                    ->first();
                if (empty($sameSocialId)) {
                    //There is no combination of this social id and provider, so create new one
                    $newSocialUser = new User;
                    $newSocialUser->email              = $email;
                    $name = explode(' ', $user->name);
                    if (count($name) >= 1) {
                        $newSocialUser->first_name = $name[0];
                    }
                    if (count($name) >= 2) {
                        $newSocialUser->last_name = $name[1];
                    }
                    $newSocialUser->full_name =  $user->name;
                    $newSocialUser->password = bcrypt(str_random(16));
                    $newSocialUser->token = str_random(64);
                    //$newSocialUser->activated = true; //!config('settings.activation');
                    $newSocialUser->status = 1; //!config('settings.activation');
                    $newSocialUser->role_id = 2;
                    $newSocialUser->profile_picture = $user->avatar;
                    $newSocialUser->save();
                    $socialData = new SocialAccount;
                    $socialData->client_id = $user->id;
                    $socialData->user_id = $newSocialUser->id;
                    $user_id = $newSocialUser->id;
                    $socialData->platform= 'twitter';
                    $socialData->token = str_random(64);
                    $socialData->save();
                    $socialUser = $newSocialUser;
                }
                else {
                    $socialUser = $sameSocialId;

                }

            }
            $user = DB::table('users')
                ->where('id', '=', $socialUser->user_id)
                ->first();
            // print_r($user);
            if (!$token = JWTAuth::fromUser($user)) {
                return Controller::returnResponse(403,'Invalid credentials, please try log in again', []);
            }
            //die($token);
            $user->token = $token;
            //array("User"=>$user);


            return Controller::returnResponse(200, "This is a valid username", array("User"=>$user));
            //end validation


        } catch (\Exception $e){
            return $e;
            // echo   $e->getMessage();
            //$arr = explode('response:', $e->getMessage());
            // return $arr[1];
            // echo   $array = json_decode(json_encode($e->getMessage()));
            // echo $array->['Client error'];
            // print_r(json_decode(json_encode($e->getMessage())));
        }
    }

    public function sync_contacts(Request $request){
          $contacts=$request->input("contacts");

        $split=explode(",",$contacts);

        for ($i=0;$i<sizeof($split);$i++){

            //$user=Users::fromMobile($split[$i]);

            //$split[$i]=preg_replace('/\s+/', '', $split[$i]);
          //  echo $split[$i];
            $split[$i]=str_replace(' ','' ,$split[1]);
            $user=DB::table('users')
                ->where('mobile_no','=','"'.$split[$i].'"');

            //$user=Users::fromMobile('"'.$split[1].'"');

         return print_r($user);
        }
    }
    public function checkUsernameAvailability(AuthUsernameAvailabilityRequest $request){
        //AuthUsernameAvailabilityRequest

        $response=Users::isDeleted($request->input("username"));
       // return print_r($response);
           if(count($response)==0 || $response->status==0){
               return Controller::returnResponse(200, "This is a valid username", ['username'=>$request->input('username')]);
           }else{
               //$message = "Username already found in our system, please try another one.";
               //return Controller::returnResponse(400, "Username already found in our system, please try another one.", ['username'=>$request->input('username')]);
               $errors=array("username"=>["Username already found in our system, please try another one"]);
               $message = "Username already found in our system, please try another one.";
               $message = $errors['username'][0];


               return Controller::returnResponse(400, $message,['errors'=>$errors]);

           }

//return Controller::returnResponse(200, "This is a valid username", ['username'=>$request->input('username')]);
    }

//    public function checkEmailAvailability(Request $request){
//
//        $response=Users::isEmailDeleted($request->input("email"));
//        // return print_r($response);
//        if(count($response)==0 || $response->status==0){
//            return Controller::returnResponse(200, "This is a valid email", ['email'=>$request->input('email')]);
//        }else{
//            //$message = "Username already found in our system, please try another one.";
//            //return Controller::returnResponse(400, "Username already found in our system, please try another one.", ['username'=>$request->input('username')]);
//            $errors=array("email"=>["Email already found in our system, please try another one"]);
//            $message = "email already found in our system, please try another one.";
//            $message = $errors['email'][0];
//
//
//            return Controller::returnResponse(400, $message,['errors'=>$errors]);
//
//        }


//    }
    // TODO: Add Test
    // TODO: Add to Services JSON
    public function sendVerificationCode(AuthSendVerificationCodeRequest $request){
        $input = $request->only(['country','mobile_no']);

        $user =  JWTUserTrait::getUserInstance();


        if($user instanceof User){
            $user->mobile_no = $input['country'] . $input['mobile_no'];

            // Send Verification Code
            try {
                $nexmoRequest = \Nexmo::verify()->start([
                    'number' => $user->mobile_no,
                    'brand'  => 'Streamix App',
                ]);

                if($nexmoRequest instanceof \Nexmo\Verify\Verification) {
                    $user->verification_code = $nexmoRequest->getRequestId();
                    $user->save();

                    $result = [];
                    $result['user'] = $user->toArray();
                    return Controller::returnResponse(200, 'Verification code sent successfully', $result);
                }
            } catch (\Exception $e){
                
                // Failed to send verification code.
                return Controller::returnResponse(400, 'Something went wrong, Please try again later', []);
            }
        }
        return Controller::returnResponse(400, 'You are not an authorized user.', []);
    }

    public function ForgetPasswordPhone(AuthSendVerificationCodeRequest $request){
        $input = $request->only(['country','mobile_no']);
        $mobile_no= $input['country'] . $input['mobile_no'];
        $user= DB::table('users')
            ->where('mobile_no',"=",$mobile_no)
            ->first();

        if(count($user)>0){
            try {
                $nexmoRequest = \Nexmo::verify()->start([
                    'number' => $mobile_no,
                    'brand'  => 'Streamix App',
                ]);

                if($nexmoRequest instanceof \Nexmo\Verify\Verification) {
                    $user->verification_code = $nexmoRequest->getRequestId();
                    unset($user->password);

                    $result = [];
                    $result['user'] = $user;//->toArray();
                    return Controller::returnResponse(200, 'Verification code sent successfully', $result);
                }
            } catch (\Exception $e){
                //return $e;
                // Failed to send verification code.
                return Controller::returnResponse(500, 'Something went wrong, Please try again later', []);
            }


        }else{
            return Controller::returnResponse(500, 'Phone number not found', []);
        }


    }
    // TODO: Add Test
    // TODO: Add to Services JSON
    public function resendCode(Request $request){
        $user =  JWTUserTrait::getUserInstance();
        if($user instanceof User){
            // Send Verification Code
            try {
                $nexmoRequest = \Nexmo::verify()->trigger($user->verification_code);
                if($nexmoRequest instanceof \Nexmo\Verify\Verification) {
                    $result = [];
                    $result['user'] = $user->toArray();
                    return Controller::returnResponse(200, 'Verification code sent successfully', $result);
                }
            } catch (\Exception $e){
                // Failed to resend verification code.
                return Controller::returnResponse(500, 'Something went wrong, Please try again later', []);
            }
        }
        return Controller::returnResponse(403, 'You are not an authorized user.', []);
    }

    // TODO: Add Test
    // TODO: Add to Services JSON
    public function verifyCode(AuthVerifyCodeRequest $request){
       //AuthVerifyCodeRequest   $input = $request->get('code', null);
        $input["code"] = $request->get('code', null);

        $user =  JWTUserTrait::getUserInstance();
        if($user instanceof User){

            // Send Verification Code to Verify
            try {

                $nexmoRequest = \Nexmo::verify()->check($user->verification_code, $input["code"]);

                if ($nexmoRequest instanceof \Nexmo\Verify\Verification) {
                    $user->is_verified = true;
                    $user->save();
                    $result = [];
                    $result['user'] = $user->toArray();
                    return Controller::returnResponse(200, 'Verification code verified successfully', $result);
                }
            } catch (\Exception $e){
                // FAILED VERIFICATION
              //  return $e;
                return Controller::returnResponse(400, 'Code cannot be verified, Please try again.', []);
            }
        }
        return Controller::returnResponse(400, 'You are not an authorized user.', []);
    }

    public function login(AuthLoginRequest $request){
        $input = $request->only(['email', 'password', 'role_id']);
        $checlk_name = $request->input("email");
        if($request->input("email") == ''){
            $checlk_name = $request->input("username");
        }
        $is_deleted=Users::isEmailDeleted($checlk_name);
        if(count($is_deleted)!=0){
            if($is_deleted->status==0){
                $message = "Your account is inactive. Register to activate";
                return Controller::returnResponse(400, "Your account is inactive. Register to activate.", ['Email'=>$request->input('email')]);
            }
        }
        if(!isset($input['role_id'])){
            $input['role_id'] = Setting::extract('app.default.user_role', 2);
        }
        $response=$this->doLogin($input);
        return $response;
        
    }

    public function renewToken(AuthRenewTokenRequest $request){
        $input            = $request->only(['user_id', 'email', 'role_id']);
        if(!isset($input['role_id'])){
            $input['role_id'] = Setting::extract('app.default.user_role', 2);
        }

        $user = User::query()->where('id', $input['user_id'])->where('email', $input['email'])->where('role_id', $input['role_id'])->first();

        if (!$token = JWTAuth::fromUser($user)) {
            return Controller::returnResponse(403,'Invalid credentials, please try log in again', []);
        }

        return $this->responseUser($token, "Token renewed successfully!");
    }

    public function forgotPassword(AuthForgotPasswordRequest $request) {

        $userRequested = User::where(['email' => $request->input('email')])->first();

            $passwordGenerated = \Illuminate\Support\Str::random(12);

            $userRequested->password = Hash::make($passwordGenerated);
            $updatedPass= Hash::make($passwordGenerated);
            $userRequested->save();
            $user=DB::table('users')
            ->where('email',"=",$userRequested->email)
            ->update(['password' =>$updatedPass ]);

            // Send reset password email
        //dd($userRequested);
            $emailBody = "You have requested to reset a password of your account, please find your new generated password below:

            New Password: " . $passwordGenerated . "

            Thanks.";
           /*$stat= \Mail::raw($emailBody, function ($m) use ($userRequested) {
                $projectName = Config::get('constants.global.site.name');
                $m->to($userRequested->email)->from('data.expert8@gmail.com')->subject('Reset Password - ' . $projectName);
            });*/
        //dd($stat);


        /*$stat =Mail::send("email.test",["name"=>"streamix"],function($message)
         {
            $message->to("data.expert9@gmail.com","Streamix App")->from("streamixapp@gmail.com")->subject("Mail sednig from laravel for streamix application");

         });*/
        //mail($userRequested->email,"My subject",$emailBody);
        Mail::send("email.test", ['key' => 'value'], function($message)
        {
            $message->to('fwadaslam77@gmail.com', 'John Smith')->subject('Welcome!');
        });

//dd($stat);


            return Controller::returnResponse(200, 'Your password has been updated. Please check your email.', []);

    }

    // TODO: Add Test
    // TODO: Add to Services JSON
   /* public function forgotPasswordPhone(AuthForgotPasswordPhoneRequest $request){
        $userRequested = User::where(['mobile_no' => $request->input('mobile_no')])->first();

        if($userRequested instanceof User){
            try {
                $nexmoRequest = \Nexmo::verify()->start([
                    'number' => $userRequested->mobile_no,
                    'brand'  => 'Streamix App',
                ]);
                if($nexmoRequest instanceof \Nexmo\Verify\Verification) {
                    $userRequested->verification_code = $nexmoRequest->getRequestId();
                    $userRequested->save();
                    $result = [];
                    $result['user'] = $userRequested->toArray();
                    return Controller::returnResponse(200, 'Verification code sent successfully', $result);
                }
            } catch (\Exception $e){
                // Failed to send verification code.
                return Controller::returnResponse(500, 'Something went wrong, Please try again later', []);
            }
        }
        return Controller::returnResponse(403, 'You are not an authorized user.', []);
    }*/

    // TODO: Add Test
    // TODO: Add Test
    // TODO: Add to Services JSON
    public function verifyForgotPasswordCode(AuthVerifyForgotPasswordCodeRequest $request){
        $input = $request->get('code', null);
        $verification = $request->get('verification', null);
        //$user =  JWTUserTrait::getUserInstance();
        $user = User::find($request->input('user_id', null));
        if($user instanceof User){
            // Send Verification Code to Verify
            try {
                $nexmoRequest = \Nexmo::verify()->check($verification, $input);
                if ($nexmoRequest instanceof \Nexmo\Verify\Verification) {
                    $result = [];
                    $result['user'] = $user->toArray();
                    return Controller::returnResponse(200, 'Verification code verified successfully', []);
                }
            } catch (\Exception $e){

                // FAILED VERIFICATION
                return Controller::returnResponse(403, 'Code cannot be verified, Please try again.', []);
            }
        }
        return Controller::returnResponse(403, 'You are not an authorized user.', []);
    }

    // TODO: Add Test
    // TODO: Add to Services JSON
    public function resetPassword(ResetPasswordRequest $request) {

        $user = User::find($request->input('user_id', null));

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
        // $message = "You have successfully changed your password. Sign in using your new password to be part of Streamix experience.";
        return Controller::returnResponse(200, 'Password changed successfully', []);

    }


    public function logout(Request $request)
    {
        $token =  JWTUserTrait::extractToken($request);
        JWTAuth::invalidate( $token );
        return Controller::returnResponse(200,'Logged out successfully!', []);
    }

    private function doLogin($userData, $isRegistered=false){

        $userData1['username'] = $userData['email'];
        $userData1['password'] = $userData['password'];
        //dd($userData);
        if (!$token = JWTAuth::attempt($userData)) {
            //print_r($userData1);
            if(!$token = JWTAuth::attempt($userData1)) {
                return Controller::returnResponse(400, 'The email or password you entered is incorrect.', []);
            }
        }
        $message = ($isRegistered) ? "User register successfully" : "User login successfully" ;
        
        return $this->responseUser($token, $message);
    }

    private function responseUser($token, $message){
        $userData = JWTAuth::toUser($token)->toArray();

        if($userData['status'] == User::STATUS_BLOCKED){
            return Controller::returnResponse(400,'The email or password you entered is incorrect', []);//User is blocked by admin
        }
        $userData['token']  = $token;
        
        $userData=$this->total_counts($userData,$userData["id"]);
        return Controller::returnResponse(200, $message, ['user'=>$userData]);
    }
    private function total_counts($userData,$user_id){
        
        $total_streams= DB::table('user_streams')
            ->where("user_id",'=',$user_id)
            ->count();
         $total_followers= DB::table('friends')
            ->where("user_id",'=',$user_id)
            ->where("type","=","20")
            ->count();
         $total_followings= DB::table('friends')
            ->where("friend_user_id",'=',$user_id)
            ->where("type","=","20")
            ->count();
         $total_friends= DB::table('user_friend_requests')
            ->where("user_id",'=',$user_id)
            ->where("status","=","10")
            ->count();
            
             $userData["total_streams"]=$total_streams;
             $userData["total_followers"]=$total_followers;
             $userData["total_followings"]=$total_followings;
             $userData["total_friends"]=$total_friends;
         return $userData;   
    }

}
