<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class CreateProfileRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'about' => 'required|string',
      'phone' => 'nullable|string',
      'specialities' => 'required|array',
      'specialities.*.category_id' => 'required|exists:App\Models\Categories\Category,id',
      'specialities.*.price' => 'required|numeric',
    ];
  }
}
