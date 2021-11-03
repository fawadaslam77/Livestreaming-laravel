<?php

namespace App\Http\Requests\Api;

class CreatePackageRequest extends FormRequest
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
            'name' => 'required|string',
            'daily_limit' => 'required|integer',
            'storage_limit' => 'required|integer',
            'expire_days' => 'required|integer',
            'dashboard' => 'required|integer|in:0,1',
            'allow_240' => 'required|integer|in:0,1',
            'allow_480' => 'required|integer|in:0,1',
            'allow_720' => 'required|integer|in:0,1',
            'allow_1080' => 'required|integer|in:0,1',
            'allow_save_offline' => 'required|integer|in:0,1',
            'disable_ads' => 'required|integer|in:0,1',
        ];
    }

}
