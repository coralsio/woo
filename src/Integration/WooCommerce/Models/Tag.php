<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\QueryBuilderTrait;

/**
 * Class Tag
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
 */
class Tag extends BaseModel
{
    use QueryBuilderTrait;

    protected $endpoint = 'products/tags';
}
