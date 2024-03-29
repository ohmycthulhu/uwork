<?php

namespace App\Http\Requests\Card;

use App\Http\Requests\ApiRequest;

class UpdateCardFormRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'label' => 'nullable|string',
      'expiration_month' => 'nullable|numeric|min:1|max:12',
      'expiration_year' => 'nullable|numeric|min:' . date('Y'),
    ];
  }
}
