<?php

namespace Corals\Modules\Woo\Providers;

use Corals\Foundation\Providers\BaseUpdateModuleServiceProvider;

class UpdateModuleServiceProvider extends BaseUpdateModuleServiceProvider
{
    protected $module_code = 'corals-woo';
    protected $batches_path = __DIR__ . '/../update-batches/*.php';
}
