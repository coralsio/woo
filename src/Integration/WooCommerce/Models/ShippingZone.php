<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\QueryBuilderTrait;
use Corals\Modules\Woo\Integration\WooCommerce\Traits\ShippingZoneTrait;

/**
 * Class ShippingZone
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
 * ShippingZone methods
 * @method static getLocations($id, $options = [])
 * @method static updateLocations($id, $data = [])
 * @method static addShippingZoneMethod($id, $data)
 * @method static getShippingZoneMethod($zone_id, $id, $options = [])
 * @method static getShippingZoneMethods($id, $options = [])
 * @method static updateShippingZoneMethod($zone_id, $id, $data = [])
 * @method static deleteShippingZoneMethod($zone_id, $id, $options = [])
 */
class ShippingZone extends BaseModel
{
    use QueryBuilderTrait, ShippingZoneTrait;

    protected $endpoint = 'shipping/zones';
}
