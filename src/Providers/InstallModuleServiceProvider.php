<?php

namespace Corals\Modules\Woo\Providers;

use Corals\Foundation\Providers\BaseInstallModuleServiceProvider;
use Corals\Modules\Woo\database\migrations\WooTables;
use Corals\Modules\Woo\database\seeds\WooDatabaseSeeder;

class InstallModuleServiceProvider extends BaseInstallModuleServiceProvider
{
    protected $module_public_path = __DIR__ . '/../public';

    protected $migrations = [
        WooTables::class,
    ];

    protected function providerBooted()
    {
        $this->createSchema();

        $wooDatabaseSeeder = new WooDatabaseSeeder();

        $wooDatabaseSeeder->run();
    }
}
