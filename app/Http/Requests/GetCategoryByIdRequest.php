<?php

namespace App\Http\Requests;

class GetCategoryByIdRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'level' => 'nullable|numeric|min:1|max:4',
        ];
    }
}
