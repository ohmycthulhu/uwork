<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiRequest;

class ChangeUserInfoRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'first_name' => 'nullable|string',
      'last_name' => 'nullable|string',
      'father_name' => 'nullable|string',
      'avatar' => 'nullable|mimes:jpeg,bmp,png|max:10485760',
      'about' => 'nullable|string',
      'birthdate' => 'nullable|date',
      'region_id' => 'nullable|numeric|exists:regions,id',
      'city_id' => 'nullable|numeric|exists:cities,id',
      'district_id' => 'nullable|numeric|exists:districts,id',
      'subway_id' => 'nullable|numeric|exists:subways,id',
      'is_male' => 'nullable|boolean',
      'email' => 'nullable|email|unique:users,email',
    ];
  }
}
