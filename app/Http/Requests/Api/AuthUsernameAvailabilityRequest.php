<?php

namespace App\Http\Requests\Api;

use App\Http\Controllers\Api\Controller;

class AuthUsernameAvailabilityRequest extends FormRequest
{

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'username.required'  => 'Username is a required field.',
           // 'username.unique'    => 'Username already found in our system, please try another one.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username'      => 'required',//|unique:users,username
        ];
    }

    public function response(array $errors)
    {
        $message = "Username already found in our system, please try another one.";
        if(isset($errors['username'])){
            $message = $errors['username'][0];
        }

        return Controller::returnResponse(400, $message,['errors'=>$errors]);
    }
}
