<?php

namespace Corals\Modules\Woo;

use Corals\Modules\Woo\Console\Commands\WooCommand;
use Corals\Modules\Woo\Facades\Woo;
use Corals\Modules\Woo\Integration\WooCommerce\IntegrationServiceProvider;
use Corals\Modules\Woo\Providers\WooAuthServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Corals\Foundation\Providers\BasePackageServiceProvider;
use Corals\Settings\Facades\Modules;

class WooServiceProvider extends BasePackageServiceProvider
{
    protected $defer = true;

    protected $packageCode = 'corals-woo';

    /**
     * Bootstrap the application events.
     *
     * @return void
     */

    public function bootPackage()
    {
        $this->commands(WooCommand::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function registerPackage()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/woo.php', 'woo');

        $this->app->register(WooAuthServiceProvider::class);
        $this->app->register(IntegrationServiceProvider::class);

        $this->app->booted(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Woo', Woo::class);
        });
    }

    public function registerModulesPackages()
    {
        Modules::addModulesPackages('corals/woo');
    }
}
