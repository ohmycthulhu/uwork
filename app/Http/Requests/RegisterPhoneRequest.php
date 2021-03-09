<?php

namespace App\Http\Requests;

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
