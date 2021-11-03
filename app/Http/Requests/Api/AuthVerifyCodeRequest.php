<?php

namespace App\Http\Requests\Api;

use App\Http\Controllers\Api\Controller;

class AuthVerifyCodeRequest extends FormRequest
{

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'code.required'      => 'Code is a required field.',
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
            'code'      => 'required',
        ];
    }
}
