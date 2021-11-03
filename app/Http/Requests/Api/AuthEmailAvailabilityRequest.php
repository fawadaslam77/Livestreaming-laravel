<?php

namespace App\Http\Requests\Api;

use App\Http\Controllers\Api\Controller;

class AuthEmailAvailabilityRequest extends FormRequest
{

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'  => 'Email is a required field.',
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
            'email'      => 'required',//|unique:users,username
        ];
    }

    public function response(array $errors)
    {
        $message = "Email already found in our system, please try another one.";
        if(isset($errors['email'])){
            $message = $errors['email'][0];
        }

        return Controller::returnResponse(400, $message,['errors'=>$errors]);
    }
}
