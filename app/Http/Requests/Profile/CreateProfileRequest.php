<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiRequest;

class CreateProfileRequest extends ApiRequest
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
      'specialities' => 'nullable|array',
      'specialities.*.category_id' => 'required|exists:App\Models\Categories\Category,id',
      'specialities.*.price' => 'required|numeric',
      'specialities.*.name' => 'nullable|string',
      'specialities.*.description' => 'nullable|string',
      'specialities.*.images' => 'nullable|array',
      'specialities.*.images.*' => 'required|numeric|min:1',
    ];
  }
}
