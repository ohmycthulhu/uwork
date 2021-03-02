<?php

namespace App\Http\Requests\Messenger;

use App\Http\Requests\FormRequest;

class SendMessageRequest extends FormRequest
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
      'attachment' => 'required_without_all:text|file'
    ];
  }
}
