<?php


namespace Corals\Modules\Woo\Integration\WooCommerce\Traits;


trait ShippingZoneTrait
{
    /**
     * Retrieve all Items.
     *
     * @param int $id
     * @param array $options
     *
     * @return array
     */
    protected function getLocations($id, $options = [])
    {
        $this->endpoint = "shipping/zones/{$id}/locations";
        self::all($options);
    }

    /**
     * Update Existing Item.
     *
     * @param int $id
     * @param array $data
     *
     * @return object
     */
    protected function updateLocations($id, $data = [])
    {
        $this->endpoint = "shipping/zones/{$id}/locations";
        self::update($data);
    }

    /**
     * Create new Item.
     *
     * @param int $id
     * @param array $data
     *
     * @return object
     */
    protected function addShippingZoneMethod($id, $data)
    {
        $this->endpoint = "shipping/zones/{$id}/methods";
        self::create($data);
    }

    /**
     * Retrieve single Item.
     *
     * @param int $zone_id
     * @param int $id
     * @param array $options
     *
     * @return object
     */
    protected function getShippingZoneMethod($zone_id, $id, $options = [])
    {
        $this->endpoint = "shipping/zones/{$zone_id}/methods";
        self::find($id, $options);
    }

    /**
     * Retrieve all Items.
     *
     * @param int $id
     * @param array $options
     *
     * @return array
     */
    protected function getShippingZoneMethods($id, $options = [])
    {
        $this->endpoint = "shipping/zones/{$id}/methods";
        self::all($options);
    }

    /**
     * Update Existing Item.
     *
     * @param int $zone_id
     * @param int $id
     * @param array $data
     *
     * @return object
     */
    protected function updateShippingZoneMethod($zone_id, $id, $data = [])
    {
        $this->endpoint = "shipping/zones/{$zone_id}/methods";
        self::update($id, $data);
    }

    /**
     * Destroy Item.
     *
     * @param int $zone_id
     * @param int $id
     * @param array $options
     *
     * @return object
     */
    protected function deleteShippingZoneMethod($zone_id, $id, $options = [])
    {
        $this->endpoint = "shipping/zones/{$zone_id}/methods";
        self::delete($id, $options);
    }
}
