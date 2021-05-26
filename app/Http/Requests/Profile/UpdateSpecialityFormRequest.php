<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiRequest;

class UpdateSpecialityFormRequest extends ApiRequest
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
      'price' => 'required|numeric|min:1|max:999999',
      'description' => 'nullable|string',

      'images_remove' => 'nullable|array',
      'images_remove.*' => 'required|numeric',

      'images_add' => 'nullable|array',
      'images_add.*' => 'required|image',
    ];
  }
}
