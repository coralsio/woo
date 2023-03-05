<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Facades;

use Corals\Modules\Woo\Integration\WooCommerce\WooCommerceApi;
use Illuminate\Support\Facades\Facade;

class WooCommerce extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return WooCommerceApi::class;
    }
}
