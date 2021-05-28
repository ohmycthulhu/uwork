<?php

namespace App\Http\Requests;

class SearchCategoriesRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'keyword' => 'required|string',
          'parent_id' => 'nullable|numeric|min:1',
        ];
    }
}
