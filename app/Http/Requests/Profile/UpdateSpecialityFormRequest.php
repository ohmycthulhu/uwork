<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class UpdateSpecialityFormRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'name' => 'nullable|string',
      'price' => 'required|numeric',
      'description' => 'nullable|string',
    ];
  }
}
