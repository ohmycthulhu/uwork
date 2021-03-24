<?php

namespace App\Http\Requests\Profile\Views;

use App\Http\Requests\FormRequest;

class ReplyReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
          'headline' => 'required|string',
          'text' => 'required|string',
        ];
    }
}