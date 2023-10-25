<?php

namespace App\Http\Requests\StandReports;

use App\Models\StandRecords;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read int $stands_records_id
 * @property-read int $publications
 * @property-read int $videos
 * @property-read int $return_visits
 * @property-read int $bible_studies
 */
class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'stands_records_id' => [
                'required',
                Rule::exists(StandRecords::TABLE, 'id'),
            ],
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
