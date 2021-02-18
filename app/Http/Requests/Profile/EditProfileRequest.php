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
      'phone' => 'nullable|string',
      'remove_specialities' => 'nullable|array',
      'remove_specialities.*' => 'required|exists:App\Models\Categories\Category,id',
      'add_specialities' => 'nullable|array',
      'add_specialities.*.category_id' => 'required|exists:App\Models\Categories\Category,id',
      'add_specialities.*.price' => 'required|numeric',
      'images' => 'nullable|array',
      'images.*' => 'required|exists:App\Models\Media\Image,id',
      'avatar' => 'nullable|file',
    ];
  }
}
