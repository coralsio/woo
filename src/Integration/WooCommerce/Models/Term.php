<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\TermTrait;

/**
 * Class Term
 * @package Corals\Modules\Woo\Integration\WooCommerce\Models
 *
 * @method static all($attribute_id, $options = [])
 * @method static find($attribute_id, $id, $options = [])
 * @method static create($attribute_id, $data)
 * @method static update($attribute_id, $id, $data)
 * @method static delete($attribute_id, $id, $options = [])
 * @method static batch($attribute_id, $data)
 */
class Term extends BaseModel
{
    use TermTrait;
    protected $endpoint;
}
