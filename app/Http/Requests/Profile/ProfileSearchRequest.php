<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class ProfileSearchRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'keyword' => 'nullable|string',
      'category_id' => 'nullable|numeric',
      'region_id' => 'nullable|numeric',
      'city_id' => 'nullable|numeric',
      'district_id' => 'nullable|numeric',
      'per_page' => 'nullable|numeric'
    ];
  }
}
