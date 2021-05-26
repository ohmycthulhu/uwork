<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest as Form;
use Illuminate\Validation\ValidationException;

class ApiRequest extends Form
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Handle a failed validation attempt.
   *
   * @param Validator $validator
   * @return void
   *
   * @throws ValidationException
   */
  protected function failedValidation(Validator $validator){
    $response = response()->json([
      'errors' => $validator->errors(),
    ], 403);

    throw (new ValidationException($validator, $response))
      ->errorBag($this->errorBag);
  }
}
