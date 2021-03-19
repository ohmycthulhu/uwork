<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'settings' => 'required|array'
        ];
    }
}
