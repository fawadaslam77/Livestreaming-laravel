<?php

namespace App\Http\Requests\Api;

use App\Http\Controllers\Api\Controller;

class AuthVerifyForgotPasswordCodeRequest extends FormRequest
{

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.user_exists'           => ':attribute field must be a valid :attribute',
            'code.required' => 'Code is a required field.',
            'verification.required'=>'Verification code is required'
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
            'user_id'   => 'required|integer|user_exists',
            'code'      => 'required',
            'verification'=>'required'
        ];
    }
}
