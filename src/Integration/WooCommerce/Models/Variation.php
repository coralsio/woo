<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\VariationTrait;

/**
 * Class Variation
 * @package Corals\Modules\Woo\Integration\WooCommerce\Models
 *
 * @method static all($product_id, $options = [])
 * @method static find($product_id, $id, $options = [])
 * @method static create($product_id, $data)
 * @method static update($product_id, $id, $data)
 * @method static delete($product_id, $id, $options = [])
 * @method static batch($product_id, $data)
 */
class Variation extends BaseModel
{
    use VariationTrait;

    protected $endpoint;
}
