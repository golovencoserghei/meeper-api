<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseItem extends Model
{
    public const TABLE = 'warehouse_items';

    protected $table = self::TABLE;

    protected $fillable = [
        'item_code',
        'name_ru',
        'name_md',
        'notes',
    ];
}
