<?php

namespace App\Http\Requests\Common;

use App\Http\Requests\ApiRequest;

class UploadImageRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'image' => 'required|mimes:jpeg,bmp,png|max:10485760',
        ];
    }
}
