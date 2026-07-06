<?php

declare(strict_types=1);

namespace LaravelSatim\Enums;

enum HttpMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
}
