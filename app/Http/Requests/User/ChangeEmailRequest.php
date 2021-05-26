<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiRequest;

class ChangeEmailRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'email' => 'required|email'
    ];
  }
}
