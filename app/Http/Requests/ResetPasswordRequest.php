<?php

namespace App\Http\Requests;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'phone' => 'required_without_all:email|string|min:11',
          'email' => 'required_without_all:phone|string',
        ];
    }
}
