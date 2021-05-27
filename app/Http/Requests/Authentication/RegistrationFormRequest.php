<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\ApiRequest;

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
      'father_name' => 'required|string|min:3|max:60',
      'password' => 'required|string',
      'email' => 'nullable|unique:users|email',
      'verification_uuid' => 'required|string|min:11',
      'avatar' => 'nullable|image|max:10485760',
      'birthdate' => 'nullable|date',
      'is_male' => 'nullable|boolean',
      'region_id' => 'nullable|numeric|exists:regions,id',
      'city_id' => 'nullable|numeric|exists:cities,id',
      'district_id' => 'nullable|numeric|exists:districts,id',
    ];
  }
}
