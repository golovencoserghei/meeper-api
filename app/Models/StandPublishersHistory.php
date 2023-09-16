<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandPublishersHistory extends Model
{
    public const TABLE = 'stands_publishers_history';

    protected $table = self::TABLE;

    protected $fillable = [
        'stand_id',
        'congregation_id',
        'day',
        'time',
        'user_1',
        'user_2',
        'date',
    ];

}
