<?php

namespace App\Http\Requests;

use App\Models\Congregation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * @property-read int $congregation_id
 */
class PublishersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'congregation_id' => [
                'required',
                Rule::exists(Congregation::TABLE, 'id')
            ],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => $validator->errors()], 422));
    }
}
