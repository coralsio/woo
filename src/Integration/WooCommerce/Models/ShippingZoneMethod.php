<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\ShippingZoneMethodTrait;

/**
 * Class ShippingZoneMethod
 * @package Corals\Modules\Woo\Integration\WooCommerce\Models
 *
 * @method static all($options = [])
 * @method static find($id, $options = [])
 */
class ShippingZoneMethod extends BaseModel
{
    use ShippingZoneMethodTrait;
    protected $endpoint = 'shipping_methods';
}
