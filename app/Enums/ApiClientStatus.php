<?php

namespace App\Enums;

enum ApiClientStatus: string
{
    case ACTIVE = 'active';
    case DISABLED = 'disabled';
}
