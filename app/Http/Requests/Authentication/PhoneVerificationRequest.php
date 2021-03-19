<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\FormRequest;

class PhoneVerificationRequest extends FormRequest
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
