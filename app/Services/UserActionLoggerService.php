<?php

namespace App\Services;

use App\Models\UsersActions;

class UserActionLoggerService
{
    public static function logAction(string $action, array $payload)
    {
        UsersActions::create([
            'user_id' => auth()->id(),
            'payload' => $payload,
            'action' => $action,
        ]);
    }
}
