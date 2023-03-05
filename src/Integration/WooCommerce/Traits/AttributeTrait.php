<?php


namespace Corals\Modules\Woo\Integration\WooCommerce\Traits;


trait AttributeTrait
{
    /**
     * Retrieve all Items.
     *
     * @param int $attribute_id
     * @param array $options
     *
     * @return array
     */
    protected function getTerms($attribute_id, $options = [])
    {
        $this->endpoint = "products/attributes/{$attribute_id}/terms";

        return self::all($options);
    }

    /**
     * Retrieve single Item.
     *
     * @param int $attribute_id
     * @param int $term_id
     * @param array $options
     *
     * @return object
     */
    protected function getTerm($attribute_id, $term_id, $options = [])
    {
        $this->endpoint = "products/attributes/{$attribute_id}/terms";

        return self::find($term_id, $options);
    }

    /**
     * Create new Item.
     *
     * @param int $attribute_id
     * @param array $data
     *
     * @return object
     */
    protected function addTerm($attribute_id, $data)
    {
        $this->endpoint = "products/attributes/{$attribute_id}/terms";

        return self::create($data);
    }

    /**
     * Update Existing Item.
     *
     * @param int $attribute_id
     * @param int $term_id
     * @param array $data
     *
     * @return object
     */
    protected function updateTerm($attribute_id, $term_id, $data)
    {
        $this->endpoint = "products/attributes/{$attribute_id}/terms";

        return self::update($term_id, $data);
    }

    /**
     * Destroy Item.
     *
     * @param int $attribute_id
     * @param int $term_id
     * @param array $options
     *
     * @return object
     */
    protected function deleteTerm($attribute_id, $term_id, $options = [])
    {
        $this->endpoint = "products/attributes/{$attribute_id}/terms";

        return self::delete($term_id, $options);
    }

    /**
     * Batch Update.
     *
     * @param int $attribute_id
     * @param array $data
     *
     * @return object
     */
    protected function batchTerm($attribute_id, $data)
    {
        $this->endpoint = "products/attributes/{$attribute_id}/terms";

        return self::batch($data);
    }
}
