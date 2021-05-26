<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiRequest;

class CreateLoginTokenRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => 'required|string|min:7',
        ];
    }
}
