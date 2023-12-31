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
 * @property-read array $stand_ids
 * @property-read string $date_day_start
 * @property-read string $date_day_end
 * @property-read string $all_weeks
 */
class StandRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'congregation_id' => [
                'required',
                Rule::exists(Congregation::TABLE, 'id')
//                Rule::prohibitedIf() @todo - make validation for 1 unique congregation and stand template
            ],
            'stand_ids' => [
                'required',
                'array',
            ],
            'stand_ids.*' => [
                'required',
                Rule::exists(Stand::TABLE, 'id')
            ],
            'date_day_start' => [
                'required',
                'date_format:d-m-Y',
            ],
            'date_day_end' => [
                'required',
                'date_format:d-m-Y',
                'after_or_equal:dateDayStart', // @todo - don't more than 7 days
            ],
            'all_weeks' => ['sometimes', 'boolean']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => $validator->errors()], 422));
    }
}
