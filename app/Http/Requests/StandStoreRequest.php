<?php

namespace App\Http\Requests;

use App\Models\Congregation;
use App\Models\Stand;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * @property-read int $congregation_id
 * @property-read int $stand_id
 * @property-read string $week_schedule
 * @property-read ?string $activation_at
 * @property-read ?int $publishers_at_stand
 */
class StandStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'congregation_id' => [
                'required',
                Rule::exists(Congregation::TABLE, 'id'),
//                Rule::prohibitedIf() @todo - make validation for 1 unique congregation and stand template
            ],
            'stand_id' => [
                'required',
                Rule::exists(Stand::TABLE, 'id')
            ],
            'week_schedule' => [
                'required',
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
