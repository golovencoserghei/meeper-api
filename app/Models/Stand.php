<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $location
 * @property string $name
 */
class Stand extends Model
{
    public const TABLE = 'stands';

    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'congregation_id',
    ];
}
