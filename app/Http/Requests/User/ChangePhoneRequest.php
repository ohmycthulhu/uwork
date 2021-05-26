<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiRequest;

class ChangePhoneRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'phone' => 'required|string|min:7',
    ];
  }
}
