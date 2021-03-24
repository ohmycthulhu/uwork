<?php

namespace App\Http\Requests;

class ReadNotificationsRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'ids' => 'nullable|array',
      'ids.*' => 'nullable|numeric',
    ];
  }
}
