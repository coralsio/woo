<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\SystemTrait;

/**
 * Class System
 * @package Corals\Modules\Woo\Integration\WooCommerce\Models
 *
 * @method static status($options = [])
 * @method static tool($id, $options = [])
 * @method static tools($options = [])
 * @method static run($id, $data)
 */
class System extends BaseModel
{
    use SystemTrait;
    protected $endpoint;
}
