<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\ApiRequest;

class LoginFormRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'email' => 'required_without_all:phone|email',
      'phone' => 'required_without_all:email|string|min:7',
      'password' => 'required|string',
    ];
  }
}
