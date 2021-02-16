<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class ChangeEmailRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'password' => 'required|string',
      'email' => 'required|email'
    ];
  }
}
