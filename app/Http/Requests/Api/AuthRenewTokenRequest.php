<?php

namespace App\Http\Requests\Api;

use App\Http\Controllers\Api\Controller;

class AuthRenewTokenRequest extends FormRequest
{

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email'                         => 'The email or password you entered is incorrect.',
            'email.user_id_email_match'     => 'The email or password you entered is incorrect.',
            'user_id'                       => 'The email or password you entered is incorrect.'
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
            'user_id'       => 'required|integer',
            // user_id_email_match validates user_id AND email in database.
            'email'         => 'required|email|user_id_email_match:user_id',
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
