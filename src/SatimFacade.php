<?php

declare(strict_types=1);

namespace LaravelSatim;

use Illuminate\Support\Facades\Facade;

class SatimFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-satim';
    }
}
