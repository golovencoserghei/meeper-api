<?php

namespace App\Http\Requests;

use App\Models\Congregation;
use App\Models\Stand;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * @property-read ?int $congregation_id
 * @property-read ?string $first_name
 * @property-read ?string $last_name
 * @property-read ?string $email
 * @property-read ?string $password
 * @property-read ?string $phone_number
 */
class PublisherUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'congregation_id' => [
                'sometimes',
                Rule::exists(Congregation::TABLE, 'id')
            ],
            'first_name' => [
                'sometimes',
                'string'
            ],
            'last_name' => [
                'sometimes',
                'string'
            ],
            'email' => [
                'sometimes',
                'email'
            ],
            'password' => [
                'sometimes',
                'string'
            ],
            'phone_number' => [
                'sometimes',
                'string'
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => $validator->errors()], 422));
    }
}
