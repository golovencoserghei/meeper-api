<?php

namespace App\Http\Requests\StandReports;

use App\Models\Congregation;
use App\Models\Stand;
use App\Models\StandRecords;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read int $publisher_id
 * @property-read int $congregation_id
 * @property-read int $stand_id
 * @property-read int $stands_records_id
 * @property-read string $date_start
 * @property-read string $date_end
 */
class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'publisher_id' => [
                'sometimes',
                Rule::exists(User::TABLE, 'id')
            ],
            'congregation_id' => [
                'sometimes',
                Rule::exists(Congregation::TABLE, 'id')
            ],
            'stand_id' => [
                'sometimes',
                Rule::exists(Stand::TABLE, 'id')
            ],
            'stands_records_id' => [
                'sometimes',
                Rule::exists(StandRecords::TABLE, 'id')
            ],
            'date_start' => [
                'sometimes',
                'date_format:d-m-Y H:i:s',
            ],
            'date_end' => [
                'sometimes',
                'date_format:d-m-Y H:i:s',
            ],
        ];
    }
}
