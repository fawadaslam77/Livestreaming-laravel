<?php

namespace App\Http\Requests\Api;

use App\User;

class UpdateDeviceRequest extends FormRequest
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
            'user_id'        => 'required|integer|user_exists|user_token',
            'device_token'   => 'required|string|min:10',
            'device_type'    => 'required|string|in:' . User::DEVICE_TYPE_IOS . ',' . User::DEVICE_TYPE_ANDROID,
        ];
    }

}
