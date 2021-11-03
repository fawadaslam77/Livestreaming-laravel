<?php

namespace App\Http\Requests\Api;

use App\Models\FriendRequestMedium;
use Illuminate\Validation\Rule;

class CreateUserFriendRequest extends FormRequest
{
    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required'            => ':attribute is a required field',
            'user_id.user_token'          => ':attribute and token mismatched',
            'user_id.user_exists'         => ':attribute field must be a valid :attribute',
            'friend_user_id.required'     => ':attribute is a required field',
            'friend_user_id.user_exists'  => ':attribute field must be a valid user id',
            'medium.in'            => 'Medium may only be one of [0=App Request,10=Phone Book,20=Email,30=Facebook,40=Twitter,50=Google Plus]',
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
            'user_id'            => [ // user_id must be verified by auth token.
                'required',
                'integer',
                'user_token',
                'user_exists',
            ],
            'friend_user_id'            => [
                'required',
                'integer',
                'user_exists',
            ],
            'medium' => [
                'required','integer',
                Rule::in([FriendRequestMedium::MEDIUM_APP_REQUEST, FriendRequestMedium::MEDIUM_PHONEBOOK, FriendRequestMedium::MEDIUM_EMAIL, FriendRequestMedium::MEDIUM_FACEBOOK, FriendRequestMedium::MEDIUM_TWITTER, FriendRequestMedium::MEDIUM_GOOGLE_PLUS]),
            ],
        ];
    }

}
