<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @property-read ?string $week_schedule
 * @property-read ?string $activation_at
 * @property-read ?int $publishers_at_stand
 */
class StandUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'week_schedule' => [
                'sometimes',
                'array',
            ],
            'activation_at' => [
                'sometimes',
                'string',
            ],
            'publishers_at_stand' => [
                'sometimes',
                'integer',
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => $validator->errors()], 422));
    }
}
