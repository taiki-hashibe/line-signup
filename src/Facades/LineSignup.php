<?php

namespace LowB\LineSignup\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \LowB\LineSignup\LineSignup
 */
class LineSignup extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \LowB\LineSignup\LineSignup::class;
    }
}
