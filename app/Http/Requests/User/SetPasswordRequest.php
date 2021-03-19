<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;

class SetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => 'required|confirmed',
        ];
    }
}
