<?php

namespace App\Http\Requests;

use App\Models\Congregation;
use App\Models\Stand;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * @property-read ?int $congregationId
 * @property-read ?string $name
 * @property-read ?string $prename
 * @property-read ?string $email
 * @property-read ?string $password
 */
class PublisherUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'congregationId' => [
                'sometimes',
                Rule::exists(Congregation::TABLE, 'id')
            ],
            'name' => [
                'sometimes',
                'string'
            ],
            'prename' => [
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
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => $validator->errors()], 422));
    }
}
