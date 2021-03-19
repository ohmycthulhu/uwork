<?php

namespace App\Http\Requests\Communication;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Facades\Auth;

class AppealFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $base = [
            'text' => 'required|string',
          'appeal_reason_id' => 'required_without_all:appeal_reason_other|nullable|numeric|exists:appeal_reasons,id',
          'appeal_reason_other' => 'required_without_all:appeal_reason_id|nullable|string',
        ];

        if (Auth::guest()) {
          $base = array_merge($base, [
            'name' => 'required|string',
            'phone' => 'required_without_all:email|nullable|string|min:9',
            'email' => 'required_without_all:phone|nullable|email'
          ]);
        }

        return $base;
    }
}
