<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiRequest;

class LoadSpecialitiesCategoriesRequest extends ApiRequest
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
