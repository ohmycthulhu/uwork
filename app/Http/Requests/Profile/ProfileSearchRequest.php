<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiRequest;

class ProfileSearchRequest extends ApiRequest
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
      'districts' => 'nullable|array',
      'districts.*' => 'required|numeric|min:0',

      'subway_id' => 'nullable|numeric',
      'subways' => 'nullable|array',
      'subways.*' => 'required|numeric|min:0',

      'per_page' => 'nullable|numeric',
      'categories' => 'nullable|array',
      'categories.*' => 'numeric',

      'price_min' => 'nullable|numeric|min:0',
      'price_max' => 'nullable|numeric|min:0',

      'rating_min' => 'nullable|numeric|min:0',
      'rating_max' => 'nullable|numeric|min:0',

      'ratings' => 'nullable|array|max:5',
      'ratings.*' => 'required',
      'ratings.*.min' => 'nullable|numeric|min:0',
      'ratings.*.max' => 'nullable|numeric|min:1',

      'sort_by' => 'nullable|string',
      'sort_dir' => 'nullable|string',
    ];
  }
}
