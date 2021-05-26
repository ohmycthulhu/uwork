<?php

namespace App\Http\Requests\Profile\Views;

use App\Http\Requests\ApiRequest;

class AddViewRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'opened' => 'nullable|bool'
        ];
    }
}
