<?php

namespace App\Http\Requests;

use App\Rules\StandPublishersStoreRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @property-read array $publishers
 */
class StandPublishersUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'publishers' => [
                'required',
                'array',
            ],
            'publishers.*' => [
                'required',
                new StandPublishersStoreRule(),
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => $validator->errors()], 422));
    }
}
