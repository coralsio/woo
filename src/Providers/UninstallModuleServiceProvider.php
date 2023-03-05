<?php

namespace Corals\Modules\Woo\Providers;

use Corals\Foundation\Providers\BaseUninstallModuleServiceProvider;
use Corals\Modules\Woo\database\migrations\WooTables;
use Corals\Modules\Woo\database\seeds\WooDatabaseSeeder;

class UninstallModuleServiceProvider extends BaseUninstallModuleServiceProvider
{
    protected $migrations = [
        WooTables::class,
    ];

    protected function providerBooted()
    {
        $this->dropSchema();

        $wooDatabaseSeeder = new WooDatabaseSeeder();

        $wooDatabaseSeeder->rollback();
    }
}
