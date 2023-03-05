<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\OrderTrait;
use Corals\Modules\Woo\Integration\WooCommerce\Traits\QueryBuilderTrait;

/**
 * Class Order
 * @package Corals\Modules\Woo\Integration\WooCommerce\Models
 * @method static all($options = [])
 * @method static find($id, $options = [])
 * @method static create($data)
 * @method static update($id, $data)
 * @method static delete($id, $options = [])
 * @method static batch($data)
 * @method static get()
 * @method static first()
 * @method static options($parameters)
 * @method static where(...$parameters)
 * @method static orderBy($name, $direction = 'desc')
 * @method static paginate($per_page, $current_page = 1, $parameters = [])
 * @method static count()
 *
 * Order Methods
 * @method static notes($order_id, $options = [])
 * @method static note($order_id, $note_id)
 * @method static createNote($order_id, $data = [])
 * @method static deleteNote($order_id, $note_id, $options = [])
 *
 * @method static refunds($order_id, $options = [])
 * @method static refund($order_id, $refund_id)
 * @method static createRefund($order_id, $data = [])
 * @method static deleteRefund($order_id, $refund_id, $options = [])
 */
class Order extends BaseModel
{
    use QueryBuilderTrait, OrderTrait;

    protected $endpoint = 'orders';
}
