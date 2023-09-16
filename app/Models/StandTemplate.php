<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StandTemplate extends Model
{
    use HasFactory;

    public const TABLE = 'stand_templates';

    protected $fillable = [
        'type',
        'week_schedule',
        'stand_id',
        'congregation_id',
    ];

    /**
     * Get all the stands for the StandTemplate
     *
     * @return HasOne
     */
    public function stand(): HasOne
    {
        return $this->hasOne(Stand::class, 'id', 'stand_id');
    }

    /**
     * Get all the congregations for the StandTemplate
     *
     * @return HasOne
     */
    public function congregation(): HasOne
    {
        return $this->hasOne(Congregation::class, 'id', 'congregation_id');
    }

    /**
     * Get the standPublishers that owns the StandTemplate
     *
     * @return HasMany
     */
    public function standPublishers(): HasMany
    {
        return $this->hasMany(StandPublishers::class);
    }
}
