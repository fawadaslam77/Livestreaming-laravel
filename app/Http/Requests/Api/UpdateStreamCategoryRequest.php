<?php

namespace App\Http\Requests\Api;

class UpdateStreamCategoryRequest extends FormRequest
{
    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
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
            'description' => 'string',
        ];
    }

}
