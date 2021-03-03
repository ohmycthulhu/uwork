<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class ChangeImageDataRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'speciality_id' => 'nullable|exists:App\Models\User\ProfileSpeciality,id'
        ];
    }
}
