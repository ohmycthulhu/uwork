<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class ChangePhoneRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'password' => 'required',
      'phone' => 'required',
    ];
  }
}