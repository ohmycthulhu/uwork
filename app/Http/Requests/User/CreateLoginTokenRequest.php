<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;

class CreateLoginTokenRequest extends FormRequest
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
