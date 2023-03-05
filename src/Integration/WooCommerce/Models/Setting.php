<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\SettingTrait;

/**
 * Class Setting
 * @package Corals\Modules\Woo\Integration\WooCommerce\Models
 * @method static all($options = [])
 * @method static option($group_id, $id, $options = [])
 * @method static options($id, $options = [])
 * @method static update($group_id, $id, $data)
 * @method static batch($id, $data)
 */
class Setting extends BaseModel
{
    use SettingTrait;
    protected $endpoint = 'settings';
}
