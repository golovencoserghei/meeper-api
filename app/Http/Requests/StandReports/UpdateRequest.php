<?php

namespace App\Http\Requests\StandReports;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read int $publications
 * @property-read int $videos
 * @property-read int $return_visits
 * @property-read int $bible_studies
 */
class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'publications' => [
                'sometimes',
                'integer',
            ],
            'videos' => [
                'sometimes',
                'integer',
            ],
            'return_visits' => [
                'sometimes',
                'integer',
            ],
            'bible_studies' => [
                'sometimes',
                'integer',
            ],
        ];
    }
}
