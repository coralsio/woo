<?php


namespace Corals\Modules\Woo\Integration\WooCommerce\Traits;


use Corals\Modules\Woo\Integration\WooCommerce\Facades\WooCommerce;

trait ShippingZoneMethodTrait
{
    /**
     * Retrieve all Items.
     *
     * @param array $options
     *
     * @return array
     */
    protected function all($options = [])
    {
        return WooCommerce::all($this->endpoint, $options);
    }

    /**
     * Retrieve single Item.
     *
     * @param int $id
     * @param array $options
     *
     * @return object
     */
    protected function find($id, $options = [])
    {
        return WooCommerce::find("{$this->endpoint}/{$id}", $options);
    }
}
