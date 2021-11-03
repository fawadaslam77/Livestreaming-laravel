<?php

namespace App\Http\Requests\Api;

use App\Http\Controllers\Api\Controller;

class AuthForgotPasswordPhoneRequest extends FormRequest
{

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'mobile_no.user_mobile_no_exists' => 'Mobile number is incorrect.'
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
            'mobile_no'      => 'required|user_mobile_no_exists',
        ];
    }

    public function response(array $errors)
    {
        $message = "Please enter valid credentials.";
        if(isset($errors['mobile_no'])){
            $message = $errors['mobile_no'][0];
        }

        return Controller::returnResponse(400, $message,['errors'=>$errors]);
    }
}
