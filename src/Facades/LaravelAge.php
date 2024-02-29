<?php

namespace Castelnuovo\LaravelAge\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Castelnuovo\LaravelAge\LaravelAge
 */
class LaravelAge extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Castelnuovo\LaravelAge\LaravelAge::class;
    }
}
