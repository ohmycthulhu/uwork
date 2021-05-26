<?php

namespace App\Http\Requests\Messenger;

use App\Http\Requests\ApiRequest;

class SearchMessageRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'keyword' => 'required|string|min:3'
        ];
    }
}
