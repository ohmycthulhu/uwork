<?php

namespace App\Http\Requests\Common;

use App\Http\Requests\FormRequest;

class UpdateImageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'order_column' => 'required|numeric'
        ];
    }
}