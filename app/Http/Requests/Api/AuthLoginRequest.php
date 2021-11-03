<?php

namespace App\Http\Requests\Api;

use App\Http\Controllers\Api\Controller;

class AuthLoginRequest extends FormRequest
{

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //'email.required'            => 'The email or password you entered is incorrect.',
            //'email.user_email_exists'   => 'The email or password you entered is incorrect.',
            'password.required'         => 'The email or password you entered is incorrect.'
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
            //'email'      => 'required',
            'password'   => 'required|string',
        ];
    }

    public function response(array $errors)
    {
        $message = "Please enter valid credentials to login";
        if(isset($errors['email'])){
            $message = $errors['email'][0];
        }

        return Controller::returnResponse(400, $message,['errors'=>$errors]);
    }
}
