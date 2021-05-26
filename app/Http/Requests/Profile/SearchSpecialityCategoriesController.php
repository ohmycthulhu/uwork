<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiRequest;

class SearchSpecialityCategoriesController extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'keyword' => 'required|string',
      'parent_id' => 'nullable|numeric|min:1',
      'size' => 'nullable|numeric|min:1|max:30',
    ];
  }
}
