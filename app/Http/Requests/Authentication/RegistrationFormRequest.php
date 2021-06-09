<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\ApiRequest;
use App\Rules\PasswordRule;

class RegistrationFormRequest extends ApiRequest
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
      'father_name' => 'nullable|string|min:3|max:60',
      'password' => ['required', 'string', 'min:6', new PasswordRule],
      'email' => 'nullable|unique:users|email',
      'verification_uuid' => 'required|string|min:11',
      'avatar' => 'nullable|image|max:10485760',
      'birthdate' => 'nullable|date',
      'is_male' => 'nullable|boolean',
      'region_id' => 'nullable|numeric|exists:regions,id',
      'city_id' => 'nullable|numeric|exists:cities,id',
      'district_id' => 'nullable|numeric|exists:districts,id',
      'subway_id' => 'nullable|numeric|exists:subways,id',
    ];
  }
}
