<?php


namespace Corals\Modules\Woo\Integration\WooCommerce\Traits;


use Corals\Modules\Woo\Integration\WooCommerce\Facades\WooCommerce;

trait NoteTrait
{
    /**
     * Retrieve all Items.
     *
     * @param int $order_id
     * @param array $options
     *
     * @return array
     */
    protected function all($order_id, $options = [])
    {
        return WooCommerce::all("orders/{$order_id}/notes", $options);
    }

    /**
     * Retrieve single Item.
     *
     * @param int $order_id
     * @param int $note_id
     * @param array $options
     *
     * @return object
     */
    protected function find($order_id, $note_id, $options = [])
    {
        return WooCommerce::find("orders/{$order_id}/notes/{$note_id}", $options);
    }

    /**
     * Create new Item.
     *
     * @param int $order_id
     * @param array $data
     *
     * @return object
     */
    protected function create($order_id, $data)
    {
        return WooCommerce::create("orders/{$order_id}/notes", $data);
    }

    /**
     * Destroy Item.
     *
     * @param int $order_id
     * @param int $note_id
     * @param array $options
     *
     * @return object
     */
    protected function delete($order_id, $note_id, $options = [])
    {
        return WooCommerce::delete("orders/{$order_id}/notes/{$note_id}", $options);
    }
}
