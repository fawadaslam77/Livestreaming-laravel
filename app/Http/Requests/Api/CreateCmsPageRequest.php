<?php

namespace App\Http\Requests\Api;

class CreateCmsPageRequest extends FormRequest
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
            'title.required'    => 'Title is required',
            'title.string'    => 'Title must be a string',
            'body.required'    => 'Body is required',
            'body.string'    => 'Body must be a string',
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
            'title' => 'required|string',
            'body' => 'required|string',
        ];
    }
}
