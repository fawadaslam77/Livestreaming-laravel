<?php

namespace App\Http\Requests\Api;

use App\Http\Controllers\Api\Controller;

class AuthRegisterRequest extends FormRequest
{

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'  => 'The email address you entered is invalid. Please enter a valid email address',
            'username.unique'    => 'Username already found in our system, please try another one.',
            'password.required' => 'Your password must be at least 6 characters'
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
            'email'      => 'required|email',////|unique:users,email
            'password'   => 'required|string|min:6',
            'full_name'  => 'required|string|max:100',
            'username'  => 'required|unique|max:100',
            //'mobile_no'  => 'nullable|numeric',
        ];
    }

    public function response(array $errors)
    {
        $message = "Please enter valid credentials to register";
        if(isset($errors['email'])){
            $message = $errors['email'][0];
        }

        return Controller::returnResponse(400, $message,['errors'=>$errors]);
    }
}
