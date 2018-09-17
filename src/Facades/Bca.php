<?php

namespace Ngungut\Bca\Facades;

use Illuminate\Support\Facades\Facade;

class Bca extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ngungut.bca';
    }
}