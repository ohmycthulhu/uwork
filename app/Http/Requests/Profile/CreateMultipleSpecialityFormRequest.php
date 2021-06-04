<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiRequest;

class CreateMultipleSpecialityFormRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
          'price' => 'required|numeric|min:1|max:999999',
          'name' => 'nullable|string',
          'description' => 'nullable|string',
          'images' => 'nullable|array',
          'images.*' => 'required|numeric|min:1',
        ];
    }
}
