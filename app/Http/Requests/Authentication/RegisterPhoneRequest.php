<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\ApiRequest;

class RegisterPhoneRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|string|min:7',
        ];
    }
}
