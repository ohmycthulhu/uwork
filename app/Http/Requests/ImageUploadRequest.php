<?php

namespace App\Http\Requests;

class ImageUploadRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'image' => 'required|file',
      'collection' => 'nullable|string'
    ];
  }
}
