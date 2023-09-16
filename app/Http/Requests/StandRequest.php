<?php

namespace App\Http\Requests;

use App\Models\Congregation;
use App\Models\Stand;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * @property-read $congregationId
 * @property-read $standId
 */
class StandRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'congregationId' => [
                'required',
                Rule::exists(Congregation::TABLE, 'id')
            ],
            'standId' => [
                'required',
                Rule::exists(Stand::TABLE, 'id')
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => $validator->errors()], 422));
    }
}
