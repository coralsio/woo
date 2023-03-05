<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\RefundTrait;

/**
 * Class Refund
 * @package Corals\Modules\Woo\Integration\WooCommerce\Models
 * @method static all($order_id, $options = [])
 * @method static find($order_id, $refund_id, $options = [])
 * @method static create($order_id, $data)
 * @method static delete($order_id, $refund_id, $options = [])
 */
class Refund extends BaseModel
{
    use RefundTrait;
    protected $endpoint;
}
