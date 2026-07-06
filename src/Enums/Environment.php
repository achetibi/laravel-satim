<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

enum Environment: string
{
    case TEST = 'test';
    case STAGING = 'staging';
    case PRODUCTION = 'prod';
}
