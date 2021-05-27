<?php

namespace App\Http\Requests\Common;

use App\Http\Requests\ApiRequest;

class ImageUploadRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'image' => 'required|image|max:10485760',
      'collection' => 'nullable|string'
    ];
  }
}
