<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\FormRequest;

class ReviewsRetrieveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'speciality_id' => 'nullable|numeric|exists:App\Models\User\ProfileSpeciality,id',
        ];
    }
}
