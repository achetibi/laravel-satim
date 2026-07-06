<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

enum OrderStatus: int
{
    case REGISTERED_NOT_PAID = 0;
    case DECLINED = -1;
    case APPROVED = 1;
    case DEPOSITED = 2;
    case REVERSED = 3;
    case REFUNDED = 4;
    case AUTHORIZATION_DECLINED = 6;
}
