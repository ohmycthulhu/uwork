<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiRequest;

class CreateComplaintRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'type_id' => 'nullable|required_without_all:reason_other|exists:complaint_types,id',
      'reason_other' => 'nullable|required_without_all:type_id|string',
      'text' => 'nullable|string',
    ];
  }
}
