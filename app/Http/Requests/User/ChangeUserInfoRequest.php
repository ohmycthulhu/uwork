<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;

class ChangeUserInfoRequest extends FormRequest
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
      'avatar' => 'nullable|file',
      'birthdate' => 'nullable|date',
      'region_id' => 'nullable|numeric|exists:regions,id',
      'city_id' => 'nullable|numeric|exists:cities,id',
      'district_id' => 'nullable|numeric|exists:districts,id',
    ];
  }
}
