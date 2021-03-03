<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class CreateReviewFormRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'headline' => 'required|string',
      'text' => 'required|string',
      'rating_quality' => 'required|numeric|min:1|max:5',
      'rating_time' => 'required|numeric|min:1|max:5',
      'rating_price' => 'required|numeric|min:1|max:5',
    ];
  }
}
