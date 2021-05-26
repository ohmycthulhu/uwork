<?php

namespace App\Http\Requests\Messenger;

use App\Http\Requests\ApiRequest;

class SendMessageRequest extends ApiRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'text' => 'required_without_all:attachment|string',
      'attachment' => 'required_without_all:text|image'
    ];
  }
}
