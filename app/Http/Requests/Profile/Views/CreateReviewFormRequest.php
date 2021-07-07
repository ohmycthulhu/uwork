<?php

namespace App\Http\Requests\Profile\Views;

use App\Http\Requests\ApiRequest;

class CreateReviewFormRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'headline' => 'nullable|string',
      'text' => 'required|string',
      'rating_quality' => 'required|numeric|min:1|max:5',
      'rating_time' => 'required|numeric|min:1|max:5',
      'rating_price' => 'required|numeric|min:1|max:5',
    ];
  }
}
