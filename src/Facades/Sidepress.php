<?php

namespace Avexsoft\Sidepress\Facades;

use Illuminate\Support\Facades\Facade;

class Sidepress extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sidepress';
    }
}
