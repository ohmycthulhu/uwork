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
      'price' => 'required|numeric|min:1|max:999999',
      'name' => 'nullable|string',
      'description' => 'nullable|string',
      'category_id' => 'required|exists:App\Models\Categories\Category,id'
    ];
  }
}
