<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class LoadSpecialitiesCategoriesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'level' => 'nullable|numeric|min:1',
        ];
    }
}
