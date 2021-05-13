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
//      'keyword' => 'nullable|string',
      'category_id' => 'nullable|numeric',
      'region_id' => 'nullable|numeric',
      'city_id' => 'nullable|numeric',
      'district_id' => 'nullable|numeric',
      'per_page' => 'nullable|numeric',
      'categories' => 'nullable|array',
      'categories.*' => 'numeric',

      'price_min' => 'nullable|numeric|min:0',
      'price_max' => 'nullable|numeric|min:0',

      'sort_by' => 'nullable|string',
      'sort_dir' => 'nullable|string',
    ];
  }
}
