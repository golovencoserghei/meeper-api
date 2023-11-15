<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property array $week_schedule
 * @property array $default_week_schedule
 * @property bool $is_last_week_default
 * @property int $congregation_id
 * @property int $stand_id
 * @property string $activation_at
 * @property StandRecords|Collection $standRecords
 */
class StandTemplate extends Model
{
    use HasFactory;

    public const TABLE = 'stand_templates';

    protected $guarded = [];

    protected $casts = [
        'week_schedule' => 'array',
        'default_week_schedule' => 'array',
        'created_at' => 'date:d-m-Y H:i:s',
        'updated_at' => 'date:d-m-Y H:i:s',
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
    public function standRecords(): HasMany
    {
        return $this->hasMany(StandRecords::class, 'stand_template_id');
    }
}
