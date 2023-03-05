<?php

namespace Corals\Modules\Woo\Integration\WooCommerce;

use Illuminate\Support\ServiceProvider;

class IntegrationServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/wooconfig.php',
            'wooconfig'
        );

        $this->app->singleton('WooCommerceApi', function () {
            return new WooCommerceApi();
        });

        $this->app->alias('Corals\Modules\Woo\Integration\WooCommerce\WooCommerceApi', 'WooCommerceApi');
    }
}
