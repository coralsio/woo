<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\ShippingMethodTrait;

/**
 * Class ShippingMethod
 * @package Corals\Modules\Woo\Integration\WooCommerce\Models
 * @method static all($options = [])
 * @method static find($id, $options = [])
 */
class ShippingMethod extends BaseModel
{
    use ShippingMethodTrait;

    protected $endpoint = 'shipping_methods';
}
