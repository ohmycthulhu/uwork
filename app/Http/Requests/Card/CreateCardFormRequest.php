<?php

namespace App\Http\Requests\Card;


use App\Http\Requests\ApiRequest;

class CreateCardFormRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'number' => 'required|string|size:16',
      'expiration_month' => 'required|numeric|min:1|max:12',
      'expiration_year' => 'required|numeric|min:' . date('Y'),
      'cvv' => 'required|numeric|min:100|max:999',
      'name' => 'required|string',
      'label' => 'nullable|string'
    ];
  }
}
