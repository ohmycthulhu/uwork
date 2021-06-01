<?php

namespace App\Http\Requests;

class VerifyPasswordResetRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'uuid' => 'required|string',
      'code' => 'required|string|size:6',
    ];
  }
}
