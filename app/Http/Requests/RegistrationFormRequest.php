<?php

namespace App\Http\Requests;

class RegistrationFormRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'first_name' => 'required|string|min:3|max:60',
      'last_name' => 'required|string|min:3|max:60',
      'father_name' => 'required|string|min:3|max:60',
      'password' => 'required|string|confirmed',
      'email' => 'nullable|unique:users|email',
      'verification_uuid' => 'required|string|min:11'
    ];
  }
}
