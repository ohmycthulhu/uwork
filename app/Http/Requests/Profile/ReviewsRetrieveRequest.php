<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiRequest;

class ReviewsRetrieveRequest extends ApiRequest
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
