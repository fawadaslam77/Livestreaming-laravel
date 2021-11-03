<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\Controller;
use App\User;
use Closure;
use Exception;
use JWTAuth;
use App\Http\Traits\JWTUserTrait;
use Config;

class authJWT {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = JWTUserTrait::extractToken();
        $guestUserToken  =  base64_encode(strtolower(Config::get('constants.global.site.name')));
        if($guestUserToken == $token && $request->input('user_id') <1) {
            return $next($request); // Authorize if user provided guest token.
        }

        $user = JWTUserTrait::getUserInstance(); // Find user by token
        if($user instanceof User) {
            if ($user->status == User::STATUS_BLOCKED) {
                return Controller::returnResponse(403, 'User is not an active user. Admin may have banned this user.', []);
            }
            return $next($request);
        } else if ($user instanceof \Illuminate\Http\JsonResponse){
            return $user;
        }
    }
}
