<?php

namespace App\Enums;

enum PromoEventType: string
{
    case IMPRESSION = 'impression';
    case CLICK = 'click';
    case DISMISS = 'dismiss';
}
