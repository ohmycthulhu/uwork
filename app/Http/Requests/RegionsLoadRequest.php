<?php

namespace App\Http\Requests;

class RegionsLoadRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'detailed' => 'nullable|boolean',
        ];
    }
}
