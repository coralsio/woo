<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\QueryBuilderTrait;
use Corals\Modules\Woo\Integration\WooCommerce\Traits\ReportTrait;

/**
 * Class Report
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
 * Report Methods
 * @method static sales($options = [])
 * @method static topSellers($options = [])
 * @method static coupons($options = [])
 * @method static customers($options = [])
 * @method static orders($options = [])
 * @method static products($options = [])
 * @method static reviews($options = [])
 */
class Report extends BaseModel
{
    use QueryBuilderTrait, ReportTrait;

    protected $endpoint = 'reports';
}
