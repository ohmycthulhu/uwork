<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiRequest;

class UpdateProfileRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'about' => 'nullable|string',
      'phone' => 'nullable|string|min:7',
    ];
  }
}
