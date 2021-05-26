<?php

namespace App\Http\Requests;

class RetrieveNotificationsRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
          'unread_only' => 'nullable|boolean',
          'amount' => 'nullable|numeric|min:1|max:40'
        ];
    }
}
