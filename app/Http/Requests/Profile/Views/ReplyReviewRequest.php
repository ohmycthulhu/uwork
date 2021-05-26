<?php

namespace App\Http\Requests\Profile\Views;

use App\Http\Requests\ApiRequest;

class ReplyReviewRequest extends ApiRequest
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
