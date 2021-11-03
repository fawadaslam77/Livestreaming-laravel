<?php

namespace App\Http\Requests\Api;


use App\Models\StreamUserAction;
use Illuminate\Validation\Rule;

class CreateStreamUserActionRequest extends FormRequest
{
    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required'      => 'User id is a required field',
            'user_id.user_token'    => ':attribute and token mismatched',
            'user_id.user_exists'   => ':attribute field must be a valid :attribute',
            'stream_id.required'    => 'Stream id is a required field',
            'stream_id.integer'     => ':attribute must be an integer',
            'stream_id.exists'      => 'provided :attribute does not exists.',
            'type.required'         => 'Type is a required field',
            'type.integer'          => ':attribute must be an integer',
            'type.in'               => 'Type may only be one of [0=Block,10=Report,20=Favorite,30=Watch Later,40=Save,50=Share,60=Like,70=Dislike,80=View]',
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
            'user_id' => 'required|integer|user_token|user_exists',
            'stream_id' => 'required|integer|exists:user_streams,id',
            'type' => [
                'required','integer',
                Rule::in([StreamUserAction::TYPE_BLOCK,StreamUserAction::TYPE_REPORT,StreamUserAction::TYPE_FAVORITE,StreamUserAction::TYPE_WATCH_LATER,StreamUserAction::TYPE_SAVE,StreamUserAction::TYPE_SHARE,StreamUserAction::TYPE_LIKE,StreamUserAction::TYPE_DISLIKE,StreamUserAction::TYPE_VIEW]),
            ],
            'details' => 'string',
        ];
    }
}