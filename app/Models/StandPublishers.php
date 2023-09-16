<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StandPublishers extends Model
{
    use HasFactory;

    public const TABLE = 'stands_publishers';

    protected $table = self::TABLE;

    protected $fillable = [
        'stand_template_id',
        'time',
        'user_1',
        'user_2',
    ];

    /**
     * Get all of the standTemplates for the StandPublishers
     *
     * @return HasOne
     */
    public function standTemplate(): HasOne
    {
        return $this->hasOne(StandTemplate::class, 'id', 'stand_template_id');
    }

    /**
     * Get the user associated with the StandPublishers
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_1');
    }

    /**
     * Get the user associated with the StandPublishers
     *
     * @return HasOne
     */
    public function user2(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_2');
    }

    /**
     * Get the user associated with the StandPublishers
     */
    public function users()
    {
        return $this->hasMany(User::class, '');
    }
}
