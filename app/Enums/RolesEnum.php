<?php

namespace App\Enums;

enum RolesEnum: string
{
    case ADMIN = 'admin';
    case RESPONSIBLE_FOR_STAND = 'responsible-for-stand';
    case PUBLISHER = 'publisher';
}
