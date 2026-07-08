<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

enum Environment: string
{
    case TESTING = 'testing';
    case STAGING = 'staging';
    case PRODUCTION = 'production';
}
