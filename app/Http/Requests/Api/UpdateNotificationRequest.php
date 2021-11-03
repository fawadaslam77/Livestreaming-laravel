<?php

namespace App\Http\Requests\Api;

class UpdateNotificationRequest extends FormRequest
{
    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.user_token'   => ':attribute and token mismatched',
            'user_id.user_exists'  => ':attribute field must be a valid :attribute',
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
            'user_id' => 'required|integer|user_exists|user_token',
            'status'  => 'required|in:0,1',
        ];
    }

}
