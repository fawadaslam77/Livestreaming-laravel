<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use JWTAuth;
use Config;
use App\User;
use App\Http\Traits\JWTUserTrait;
use App\Http\Controllers\Api\Controller;

class AuthAdminJWT {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = JWTUserTrait::getUserInstance();
        if($user instanceof User) {
            if(!$user->role->is_admin){
                return Controller::returnResponse(403, 'You must be admin to perform this action.', []);
            }
        } else if ($user instanceof \Illuminate\Http\JsonResponse){
            return $user;
        }
        return $next($request);
    }
}
