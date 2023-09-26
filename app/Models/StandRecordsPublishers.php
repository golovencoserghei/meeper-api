<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StandRecordsPublishers extends Model
{
    use HasFactory;

    public const TABLE = 'stands_records_publishers';

    protected $table = self::TABLE;

    protected $guarded = ['*'];

    /**
     * Get the user associated with the StandRecords
     */
    public function publishers() // @todo - refactor to many to may
    {
        return $this->hasManyThrough(User::class, '');
    }
}
