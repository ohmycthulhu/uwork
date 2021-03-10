<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class CreateSpecialityFormRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'price' => 'required|numeric',
      'name' => 'nullable|string',
      'category_id' => 'required|exists:App\Models\Categories\Category,id'
    ];
  }
}
