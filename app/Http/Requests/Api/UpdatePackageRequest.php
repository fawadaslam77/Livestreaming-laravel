<?php

namespace App\Http\Requests\Api;

class UpdatePackageRequest extends FormRequest
{
    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'    => 'Name is required',
            'name.string'    => 'Name must be a string',
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
            'name' => 'string',
            'daily_limit' => 'integer',
            'storage_limit' => 'integer',
            'expire_days' => 'integer',
            'dashboard' => 'integer|in:0,1',
            'allow_240' => 'integer|in:0,1',
            'allow_480' => 'integer|in:0,1',
            'allow_720' => 'integer|in:0,1',
            'allow_1080' => 'integer|in:0,1',
            'allow_save_offline' => 'integer|in:0,1',
            'disable_ads' => 'integer|in:0,1',
        ];
    }

}
