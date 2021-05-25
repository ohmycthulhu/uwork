<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class CreateSpecialityFormRequest extends CreateMultipleSpecialityFormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return array_merge(parent::rules(), [
      'category_id' => 'required|exists:App\Models\Categories\Category,id'
    ]);
  }
}
