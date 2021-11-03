<?php

namespace App\Http\Requests\Api;
use App\Http\Controllers\Api\Controller;

class UserExistVerificationRequest extends FormRequest
{
    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            
            
            'user_id.user_exists'           => 'User Does Not Exist',
           
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
           
            'user_id'           => 'required|integer|user_exists',
           
        ];

        return $rules;
    }
    public function response(array $errors)
    {
        $message = "Please Provide Valid User ID";
        if(isset($errors['user_id'])){
            $message = $errors['user_id'][0];
        }

        return Controller::returnResponse(400, $message,['errors'=>$errors]);
    }

}
