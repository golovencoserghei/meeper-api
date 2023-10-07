<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $role_id
 * @property int $user_id
 */
class RoleAssignUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'role_id' => [
                'required',
                Rule::exists(config('permission.table_names.roles'), 'id'),
            ],
            'user_id' => [
                'required',
                Rule::exists(User::TABLE, 'id'),
            ],
        ];
    }
}
