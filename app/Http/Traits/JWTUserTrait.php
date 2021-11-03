<?php

namespace App\Http\Traits;

use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\Request;
use App\Helpers\RESTAPIHelper;
use App\User;
use Config;
use App\Setting;
// use JWTAuth;

trait JWTUserTrait {

    protected static $userInstance;

    public static function getAllHeaders()
    {
        if (!function_exists('getallheaders')) {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        } else {
            return getallheaders();
        }
    }
    /**
     * Extract token value from request
     *
     * @return string|Request
     */
    public static function extractToken($request = false) {
        $token = "";
        if ($request && $request instanceof Request) {
            $token = $request->only('token');
        } else if (is_string($request)) {
           // $token = $request;
        } else if (self::getAllHeaders()) {
            $headers[] = self::getAllHeaders();
            if (isset($headers[0]['token'])) {
                $token = $headers[0]['token'];
            } else if (isset($headers[0]['Token'])){
                $token = $headers[0]['Token'];
            }
        } else {
           // $token = Request::get('_token');
            $token = (\Request::header('Token')) ? \Request::header('Token') : "";
        }
        return $token;
    }

    /**
     * Return User instance or false if not exist in DB
     *
     * @return string|Request
     */
    public static function getUserInstance($request = false) {

        if (!self::$userInstance) {
            $token = self::extractToken($request);
            try {
                $guestUserToken  =  base64_encode(strtolower(Config::get('constants.global.site.name')));
                if($guestUserToken == $token) {
                    $input['role_id'] = 1;
                    $input['status'] = User::STATUS_ACTIVE;
                    $input['password'] = 'password';
                    $token = \JWTAuth::attempt($input);
                }

                self::$userInstance = \JWTAuth::toUser($token);
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e ){
                return Controller::returnResponse(403, 'Invalid token.', []);
            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e ) {
                return Controller::returnResponse(403, 'Your token has been expired, please log-in again.', []);
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e ) {
                return Controller::returnResponse(401, 'Token parameter not found', ['exception'=>$e->getMessage()]);
            } catch (\Exception $e) {
                if ( null === $token ) {
                    return Controller::returnResponse(401, 'Token parameter not found', []);
                } else {
                    return Controller::returnResponse(500, 'Something went wrong!', ['exception'=>$e->getMessage()]);
                }
            }
        }
        return self::$userInstance;
    }

}
