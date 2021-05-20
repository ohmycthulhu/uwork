<?php

namespace App\Http\Requests\Common;

use App\Http\Requests\FormRequest;

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
      'image' => 'required|image',
      'collection' => 'nullable|string'
    ];
  }
}
