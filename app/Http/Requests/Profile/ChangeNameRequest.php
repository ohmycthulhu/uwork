<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class ChangeNameRequest extends FormRequest
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
    ];
  }
}
