<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemsToWarehouseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'item_code' => ['sometimes', 'string'],
            'name_ru' => [
                Rule::requiredIf(!$this->has('name_md')),
                'string'
            ],
            'name_md' => [
                Rule::requiredIf(!$this->has('name_ru')),
                'string'
            ],
            'notes' => ['sometimes', 'string'],
        ];
    }
}
