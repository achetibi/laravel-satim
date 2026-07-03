<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

enum SatimOrderStatus: int
{
    case RegisteredNotPaid = 0;
    case Declined = -1;
    case Approved = 1;
    case Deposited = 2;
    case Reversed = 3;
    case Refunded = 4;
    case AuthorizationDeclined = 6;
}
