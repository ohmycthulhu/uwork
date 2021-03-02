<?php

namespace App\Http\Requests\Messenger;

use App\Http\Requests\FormRequest;

class SearchMessageRequest extends FormRequest
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
