<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\NoteTrait;

/**
 * Class Note
 * @package Corals\Modules\Woo\Integration\WooCommerce\Models
 * @method static all($order_id, $options = [])
 * @method static find($order_id, $note_id, $options = [])
 * @method static create($order_id, $data)
 * @method static delete($order_id, $note_id, $options = [])
 */
class Note extends BaseModel
{
    use NoteTrait;

    protected $endpoint;
}
