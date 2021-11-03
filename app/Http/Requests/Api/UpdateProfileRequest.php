<?php

namespace App\Http\Requests\Api;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'password.confirmed'            => 'Password must be confirmed with password_confirmation parameter',
            'user_id.user_token'            => ':attribute and token mismatched',
            'user_id.user_exists'           => ':attribute field must be a valid :attribute',
            'gender.in'                     => ":attribute may be 'male' or 'female'",
            'old_password.user_password'    => ':attribute did not matched with user',
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
            //'username'          => 'unique:users,username',
            'user_id'           => 'required|integer|user_exists|user_token',
            'full_name'         => 'string',
            //'mobile_no'         => 'string',
            'password'          => 'string|min:6|confirmed',
            'old_password'      => 'string|min:6|required_with:password|user_password:user_id',
            'status_text'       => 'string',
            'gender'            => 'string|in:male,female',
            'profile_picture'   => 'file',
        ];

        if($this->route()->getName() == "withUsername"){
          //  $rules['username']        = 'required|unique:users,username';
           // $rules['profile_picture'] = 'required|file';
            $rules['profile_picture'] = 'file';
        }

        return $rules;
    }

}
