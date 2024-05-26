<?php

namespace Castelnuovo\LaravelAge\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Castelnuovo\LaravelAge\Age
 */
class Age extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Castelnuovo\LaravelAge\Age::class;
    }
}
