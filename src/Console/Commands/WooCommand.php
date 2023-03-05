<?php

namespace Corals\Modules\Woo\Console\Commands;

use Corals\Modules\Marketplace\Models\Store;
use Corals\Modules\Woo\Integration\WooCommerce\Mappers\AttributeMapper;
use Corals\Modules\Woo\Integration\WooCommerce\Mappers\BaseMapper;
use Corals\Modules\Woo\Integration\WooCommerce\Mappers\CategoryMapper;
use Corals\Modules\Woo\Integration\WooCommerce\Mappers\CustomerMapper;
use Corals\Modules\Woo\Integration\WooCommerce\Mappers\OrderMapper;
use Corals\Modules\Woo\Integration\WooCommerce\Mappers\ProductMapper;
use Corals\Modules\Woo\Integration\WooCommerce\Mappers\TagMapper;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class WooCommand extends Command
{
    protected $signature = 'woo:load {module} {store?} {--mapper=}';
    protected $description = 'Woo loader';


    /**
     * @throws Exception
     */
    public function handle()
    {
        $module = $this->argument('module');

        switch (strtolower($module)) {
            case 'm':
            case 'marketplace':
            case 'mp':
                $storeId = $this->argument('store');

                if (!$storeId || !Store::query()->where('id', $storeId)->exists()) {
                    $this->error('Please set the store id in the command.');
                    return;
                }

                $this->loadWoo(['module' => 'Marketplace', 'store_id' => $storeId]);
                break;
            case 'ecommerce':
                $this->loadWoo(['module' => 'Ecommerce']);
                break;
            default:
                $this->error('Invalid Module: ' . $module);
        }
    }

    /**
     * @param array $args
     * @throws Exception
     */
    protected function loadWoo($args = [])
    {
        $mappers = [
            'customer' => CustomerMapper::class,
            'category' => CategoryMapper::class,
            'attribute' => AttributeMapper::class,
            'tag' => TagMapper::class,
            'product' => ProductMapper::class,
            'order' => OrderMapper::class,
        ];

        if (!is_null($this->argument('mapper'))) {
            $mappers = Arr::only($mappers, [$this->argument('mapper')]);
        }

        foreach ($mappers as $mapper) {
            $this->line(class_basename($mapper) . ' Started');
            tap(new $mapper($args), function (BaseMapper $mapperObject) use ($args) {
                $mapperObject->logProcess("=============================================");
                $mapperObject->logProcess("Start with Mapper: " . class_basename($mapperObject));
                $mapperObject->setMapperOptions($args);
                $mapperObject->fetchAll();
            });
        }
    }
}
