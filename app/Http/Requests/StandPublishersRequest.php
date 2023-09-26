<?php

namespace App\Http\Requests;

use App\Models\StandTemplate;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property-read int $stand_template_id
 * @property-read array $publishers
 * @property-read int $time
 */
class StandPublishersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'stand_template_id' => [
                'required',
                Rule::exists(StandTemplate::TABLE, 'id')
            ],
            'publishers' => [
                'required',
                'array',
            ],
            'publishers.*' => [
                'required',
                Rule::exists(User::TABLE, 'id')
            ],
            'date_time' => [
                'required',
                'date_format:d-m-Y H:i:s',
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
