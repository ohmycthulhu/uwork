<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class ChangePasswordRequest extends FormRequest
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
      'password' => 'required|string|confirmed',
    ];
  }
}
