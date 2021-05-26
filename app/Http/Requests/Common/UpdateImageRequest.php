<?php

namespace App\Http\Requests\Common;

use App\Http\Requests\ApiRequest;

class UpdateImageRequest extends ApiRequest
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
