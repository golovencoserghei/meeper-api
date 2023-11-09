<?php

namespace App\Services;

use App\Enums\UserActionEnum;
use App\Models\UsersActions;

class UserActionLoggerService
{
    public static function logAction($action, array $payload): void
    {
        $result = [
            'user_id' => auth()->id(),
            'payload' => json_encode($payload),
            'action' => $action,
        ];
        UsersActions::query()->create($result);
    }
}
