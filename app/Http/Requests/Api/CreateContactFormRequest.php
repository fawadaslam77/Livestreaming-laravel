<?php

namespace App\Http\Requests\Api;

class CreateContactFormRequest extends FormRequest
{
    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required'     => 'User id is a required field',
            'user_id.user_token'   => ':attribute and token mismatched',
            'user_id.user_exists'  => ':attribute field must be a valid :attribute',
            'email.required'       => 'Email address is a required field',
            'comments.required'    => 'Comments is a required field',
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
            //'user_id' => 'required|exists',
            'user_id'            => [ // user_id must be verified by auth token.
                'required',
                'integer',
                //'user_token',
                'user_exists',
            ],
            'email' => 'required|email',
            'comments' => 'required|string',
        ];
    }
}
