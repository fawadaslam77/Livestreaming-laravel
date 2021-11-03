<?php

namespace App\Http\Requests\Api;

use App\Http\Controllers\Api\Controller;
use App\Http\Traits\JWTUserTrait;
use App\Models\Setting;
use App\User;
use JWTAuth;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class FormRequest extends LaravelFormRequest
{

    /**
     * @inheritdoc
     */
    public function __construct() {
        $this->validator = app('validator');

        $this->extendValidator($this->validator);

        parent::__construct();
    }

    public function extendValidator($validator){
        // validates user_token with user id provided by the request
        $validator->extend('user_token', function($attribute, $value, $parameters) {
            $user = JWTUserTrait::getUserInstance(false);
            return (($user instanceof User) && $user->id == $value);
        });

        $validator->extend('user_exists', function($attribute, $value, $parameters) {
            $user = User::query()->where(['id'=>$value])->where('deleted_at', null) // Not a Deleted User
            //->where('role_id', Setting::extract('app.default.user_role', 2)) // Must be an App user
            ->where('status', User::STATUS_ACTIVE)->first(); // Must not be banned by admin
            return ($user);
        });

        $validator->extend('user_email_exists', function($attribute, $value, $parameters) {
            $user = User::query()->where(['email'=>$value])->first();
            return ($user);
        });

        $validator->extend('user_mobile_no_exists', function($attribute, $value, $parameters) {
            $user = User::query()->where(['mobile_no'=>$value])->first();
            return ($user);
        });

        $validator->extend('user_id_email_match', function($attribute, $value, $parameters) {
            $userId = $this->request->get($parameters[0]);
            $user = User::query()->where('id',$userId)->where('email' ,$value)->first();
            return ($user);
        });
        $validator->extend('user_password', function($attribute, $value, $parameters) {
            $userId = $this->request->get($parameters[0]);
            $token = JWTAuth::attempt(['id'=>$userId, 'password'=>$value]);
            return ($token);
        });

        // Add a required validator
        /*$validator->extendImplicit('organization_type', function($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $requiredType= $parameters[0];
            if(isset($data['organization_id'])) {
                $organization = Organization::find($data['organization_id']);
                if ($organization) {
                    if ($organization->type == $requiredType) {
                        return (!empty($value));
                    } else {
                        return true;
                    }
                }
            }
        });*/
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Get the proper failed validation response for the request.
     *
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(array $errors)
    {
        return Controller::returnResponse(400, "Form Validation Errors",['errors'=>$errors]);
    }
}