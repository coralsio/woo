<?php


namespace Corals\Modules\Woo\Integration\WooCommerce\Traits;


use Corals\Modules\Woo\Integration\WooCommerce\Facades\WooCommerce;

trait SettingTrait
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
     * Retrieve option.
     *
     * @param int   $group_id
     * @param int   $id
     * @param array $options
     *
     * @return array
     */
    protected function option($group_id, $id, $options = [])
    {
        return WooCommerce::find("settings/{$group_id}/{$id}", $options);
    }

    /**
     * Retrieve options.
     *
     * @param int   $id
     * @param array $options
     *
     * @return array
     */
    protected function options($id, $options = [])
    {
        return WooCommerce::find("settings/{$id}", $options);
    }

    /**
     * Update Existing Item.
     *
     * @param int   $group_id
     * @param int   $id
     * @param array $data
     *
     * @return object
     */
    protected function update($group_id, $id, $data)
    {
        return WooCommerce::update("settings/{$group_id}/{$id}", $data);
    }

    /**
     * Batch Update.
     *
     * @param array $data
     *
     * @return object
     */
    protected function batch($id, $data)
    {
        return WooCommerce::create("settings/{$id}/batch", $data);
    }
}
