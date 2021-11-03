<?php

namespace App\Http\Requests\Api;

class UpdateCmsPageRequest extends FormRequest
{
    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name'    => 'Name must be a string',
            'title'    => 'Title must be a string',
            'body'    => 'Body must be a string',
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
            'title' => 'string',
            'body' => 'string',
        ];
    }
}
