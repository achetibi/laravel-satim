<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

enum SatimFundingType: string
{
    case BILL_PAYMENT = 'CP';
    case BILL_PAYMENT_698 = '698';
}
