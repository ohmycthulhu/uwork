<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class CreateMultipleSpecialityFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
          'price' => 'required|numeric|min:1|max:999999',
          'name' => 'nullable|string',
          'description' => 'nullable|string',
        ];
    }
}
