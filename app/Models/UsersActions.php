<?php

namespace App\Models;

use App\Enums\UserActionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersActions extends Model
{
    use HasFactory;

    protected $table = 'users_actions_history';

    protected $fillable = [
        'user_id',
        'action_time',
        'payload',
        'action',
    ];

//    protected $casts = [
//        'action' => UserActionEnum::class,
//    ];
}
