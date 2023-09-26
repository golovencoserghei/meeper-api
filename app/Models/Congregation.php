<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Congregation extends Model
{
    use HasFactory;

    public const TABLE = 'congregations';

    protected $guarded = ['*'];

    /**
     * Get all the stands for the Congregation
     *
     * @return HasMany
     */
    public function stands(): HasMany
    {
        return $this->hasMany(Stand::class, 'stand_id', 'id');
    }
}
