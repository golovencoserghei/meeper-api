<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StandRecords extends Model
{
    use HasFactory;

    public const TABLE = 'stands_records';

    protected $table = self::TABLE;

    protected $guarded = [];

    protected $casts = [
        'date_time' => 'date:d-m-Y H:i',
    ];

    /**
     * Get all the standTemplates for the StandRecords
     *
     * @return HasOne
     */
    public function standTemplate(): HasOne
    {
        return $this->hasOne(StandTemplate::class, 'id', 'stand_template_id');
    }

    /**
     * Get the user associated with the StandRecords
     */
    public function publishers(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                User::class,
                'stands_records_publishers',
                'stands_records_id',
                'publisher_id',
            )
            ->withTimestamps();
    }
}
