<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $role_id
 * @property array $permission_ids
 */
class RoleAssignPermissionRequest extends FormRequest
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
            'permission_ids' => [
                'required',
                'array',
            ],
            'permission_ids.*' => [
                'required',
                Rule::exists(config('permission.table_names.permissions'), 'id'),
            ],
        ];
    }
}
