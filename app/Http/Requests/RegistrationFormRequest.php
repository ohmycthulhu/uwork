<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

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
      'email' => 'required|unique:users|email',
      'phone' => 'required|unique:users|string|min:11'
    ];
  }
}
