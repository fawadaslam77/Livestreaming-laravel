<?php

namespace App\Http\Requests\Api;

use App\Models\UserStream;
use Illuminate\Validation\Rule;

class CreateUserStreamRequest extends FormRequest
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
            'name.required'        => 'Name is required',
            'name.string'          => 'Name must be a string',
            'quality.in'           => 'Quality may only be one of [240,480,720,1080]',
            'is_public.in'         => 'Is Public may be 0 or 1',
            'allow_comments.in'    => 'Allow Comments may be 0 or 1',
            'allow_tag_requests.in'=> 'Allow Tag Requests may be 0 or 1',
            'available_later.in'   => 'Available Later may be 0 or 1',
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
            'name' => 'required|string',
            'quality' => [
                'required','integer',
                Rule::in([UserStream::QUALITY_240, UserStream::QUALITY_480, UserStream::QUALITY_720, UserStream::QUALITY_1080]),
            ],
            'is_public' => 'required|integer|in:0,1',
            'allow_comments' => 'required|integer|in:0,1',
            'allow_tag_requests' => 'required|integer|in:0,1',
            'available_later' => 'required|integer|in:0,1',
            'lng'=>'numeric', // Not Required, because some user does not have location service enabled
            'lat'=>'numeric', // Not Required, because some user does not have location service enabled
        ];
    }

}
