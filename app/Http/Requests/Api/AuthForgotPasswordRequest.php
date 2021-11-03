<?php

namespace App\Http\Requests\Api;

use App\Http\Controllers\Api\Controller;

class AuthForgotPasswordRequest extends FormRequest
{

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'            => 'The email address you entered does not exist. Please enter a valid email address',
            'email.user_email_exists'   => 'The email address you entered does not exist. Please enter a valid email address',
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
            'email'      => 'required|email|user_email_exists',
        ];
    }

    public function response(array $errors)
    {
        $message = "Please enter valid credentials.";
        if(isset($errors['email'])){
            $message = $errors['email'][0];
        }

        return Controller::returnResponse(400, $message,['errors'=>$errors]);
    }
}
