<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StandReports extends Model
{
    public const TABLE = 'stands_reports';

    protected $table = self::TABLE;

    protected $fillable = [
        'reported_by',
        'report_date',
        'publications',
        'videos',
        'return_visits',
        'bible_studies',
        'stands_records_id',
        'congregation_id',
        'stand_id',
    ];

    protected $casts = [
        'report_date' => 'date:d-m-Y H:i',
    ];
}
