<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\ApiRequest;

class PhoneVerificationRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|size:6'
        ];
    }
}
