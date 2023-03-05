<?php

namespace Corals\Modules\Woo\Facades;

use Illuminate\Support\Facades\Facade;

class Woo extends Facade
{
    /**
     * @return mixed
     */
    protected static function getFacadeAccessor()
    {
        return \Corals\Modules\Woo\Classes\Woo::class;
    }
}
