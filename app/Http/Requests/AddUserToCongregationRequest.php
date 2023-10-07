<?php

namespace App\Http\Requests;

use App\Models\Congregation;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read string $user_code
 * @property-read int $user_id
 * @property-read int $congregation_id
 */
class AddUserToCongregationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_code' => [
                'string',
                Rule::exists(User::TABLE, 'code'),
                Rule::requiredIf(!$this->user_id),
            ],
            'user_id' => [
                'integer',
                Rule::exists(User::TABLE, 'id'),
                Rule::requiredIf(!$this->user_code)
            ],
            'congregation_id' => [
                'required',
                Rule::exists(Congregation::TABLE, 'id'),
            ],
        ];
    }
}
