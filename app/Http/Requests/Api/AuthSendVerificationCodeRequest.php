<?php

namespace App\Http\Requests\Api;

use App\Http\Controllers\Api\Controller;

class AuthSendVerificationCodeRequest extends FormRequest
{

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'country.required'      => 'Country is a required field.',
            'mobile_no.required'    => 'Mobile Number is a required field',
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
            'country'      => 'required',
            'mobile_no'    => 'required',
        ];
    }
}
