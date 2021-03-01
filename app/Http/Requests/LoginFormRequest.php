<?php

namespace App\Http\Requests;

class LoginFormRequest extends FormRequest
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
      'phone' => 'required_without_all:email|string|min:11',
      'password' => 'required|string',
    ];
  }
}
