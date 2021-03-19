<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class EditProfileRequest extends FormRequest
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
      'phone' => 'nullable|string|min:11',
    ];
  }
}
