<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiRequest;
use App\Rules\PasswordRule;

class ChangePasswordRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'current_password' => 'required|string',
      'password' => ['required', 'string', 'min:6', new PasswordRule],
    ];
  }
}
