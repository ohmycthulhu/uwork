<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class UploadImageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'image' => 'required|file',
        ];
    }
}
