<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\FormRequest;

class RegisterPhoneRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|string|min:11|unique:users,phone',
        ];
    }
}
