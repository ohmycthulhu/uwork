<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiRequest;

class UpdateSettingsRequest extends ApiRequest
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
