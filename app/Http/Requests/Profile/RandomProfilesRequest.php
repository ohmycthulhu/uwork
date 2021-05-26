<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiRequest;

class RandomProfilesRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'category_id' => 'nullable|numeric',
      'amount' => 'nullable|numeric|max:16'
    ];
  }
}
